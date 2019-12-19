<?php

namespace webignition\BasilParser\ValueExtractor;

class DescendantPageElementIdentifierExtractor
{
    private const PARENT_PREFIX = '{{ ';
    private const PARENT_SUFFIX = ' }}';
    private const PARENT_MATCH_LENGTH = 3;

    private $pageElementIdentifierExtractor;

    public function __construct(PageElementIdentifierExtractor $pageElementIdentifierExtractor)
    {
        $this->pageElementIdentifierExtractor = $pageElementIdentifierExtractor;
    }

    public static function createExtractor(): DescendantPageElementIdentifierExtractor
    {
        return new DescendantPageElementIdentifierExtractor(
            new PageElementIdentifierExtractor()
        );
    }

    public function extract(string $string): ?string
    {
        if (self::PARENT_PREFIX !== substr($string, 0, strlen(self::PARENT_PREFIX))) {
            return null;
        }

        $parentSuffixPosition = $this->findParentSuffixPosition($string);
        if (null === $parentSuffixPosition) {
            return null;
        }

        $parentReference = mb_substr($string, 0, $parentSuffixPosition + strlen(self::PARENT_SUFFIX));
        $parentReferenceIdentifier = $this->unwrap($parentReference);

        if (false === $this->isParentReference($parentReferenceIdentifier)) {
            return null;
        }

        $childReferencePart = mb_substr($string, mb_strlen($parentReference) + 1);
        $childReference = $this->pageElementIdentifierExtractor->extract($childReferencePart);

        if (null === $childReference) {
            return null;
        }

        return $parentReference . ' ' . $childReference;
    }

    private function isParentReference(string $string): bool
    {
        if (null !== $this->extract($string)) {
            return true;
        }

        if (null !== $this->pageElementIdentifierExtractor->extract($string)) {
            return true;
        }

        return false;
    }

    private function findParentSuffixPosition(string $string): ?int
    {
        $position = null;
        $depth = 0;
        $characters = preg_split('//u', $string, -1, PREG_SPLIT_NO_EMPTY);
        if (false === $characters) {
            return null;
        }

        $previousCharacters = implode('', array_slice($characters, 0, self::PARENT_MATCH_LENGTH));
        $characters = array_slice($characters, self::PARENT_MATCH_LENGTH);

        foreach ($characters as $index => $character) {
            if (self::PARENT_PREFIX === $previousCharacters) {
                $depth++;
            }

            if (self::PARENT_SUFFIX === $previousCharacters) {
                $depth--;
            }

            if ($depth === 0) {
                return $index;
            }

            $previousCharacters .= $character;
            $previousCharacters = mb_substr($previousCharacters, 1);
        }

        return null;
    }

    private function unwrap(string $wrappedIdentifier): string
    {
        return mb_substr(
            $wrappedIdentifier,
            self::PARENT_MATCH_LENGTH,
            mb_strlen($wrappedIdentifier) - self::PARENT_MATCH_LENGTH - self::PARENT_MATCH_LENGTH
        );
    }
}
