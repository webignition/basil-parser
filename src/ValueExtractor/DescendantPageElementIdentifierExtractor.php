<?php

namespace webignition\BasilParser\ValueExtractor;

class DescendantPageElementIdentifierExtractor
{
    private const PARENT_PREFIX = '{{ ';
    private const PARENT_SUFFIX = ' }} ';

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

    public function handles(string $string): bool
    {
        return self::PARENT_PREFIX === substr($string, 0, strlen(self::PARENT_PREFIX));
    }

    public function extract(string $string): string
    {
        if (!$this->handles($string)) {
            return '';
        }

        $parentSuffixPosition = mb_strpos($string, self::PARENT_SUFFIX);

        if (false === $parentSuffixPosition) {
            return '';
        }

        $parentReference = mb_substr($string, 0, $parentSuffixPosition + strlen(self::PARENT_SUFFIX));
        if (false === $this->isParentReference($parentReference)) {
            return '';
        }

        $childReferencePart = mb_substr($string, mb_strlen($parentReference));

        $childReference = $this->pageElementIdentifierExtractor->extract($childReferencePart);
        if ('' === $childReference) {
            return '';
        }

        return $parentReference . $childReference;
    }

    private function isParentReference(string $string): bool
    {
        return '' !== $this->pageElementIdentifierExtractor->extract(trim($string, '{} '));
    }
}
