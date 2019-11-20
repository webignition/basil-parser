<?php

declare(strict_types=1);

namespace webignition\BasilParser;

use webignition\BasilDataStructure\Assertion;
use webignition\BasilDataStructure\AssertionInterface;
use webignition\BasilParser\ValueExtractor\LiteralValueExtractor;
use webignition\BasilParser\ValueExtractor\PageElementIdentifierExtractor;
use webignition\BasilParser\ValueExtractor\QuotedValueExtractor;
use webignition\BasilParser\ValueExtractor\VariableValueExtractor;

class AssertionParser
{
    private const COMPARISONS = [
        'excludes',
        'includes',
        'is-not',
        'is',
        'exists',
        'not-exists',
        'matches',
    ];

    private $quotedValueExtractor;
    private $literalValueExtractor;
    private $pageElementIdentifierExtractor;
    private $variableValueExtractor;

    public function __construct(
        QuotedValueExtractor $quotedValueExtractor,
        LiteralValueExtractor $literalValueExtractor,
        PageElementIdentifierExtractor $pageElementIdentifierExtractor,
        VariableValueExtractor $variableValueExtractor
    ) {
        $this->quotedValueExtractor = $quotedValueExtractor;
        $this->literalValueExtractor = $literalValueExtractor;
        $this->pageElementIdentifierExtractor = $pageElementIdentifierExtractor;
        $this->variableValueExtractor = $variableValueExtractor;
    }

    public static function create(): AssertionParser
    {
        return new AssertionParser(
            new QuotedValueExtractor(),
            new LiteralValueExtractor(),
            new PageElementIdentifierExtractor(),
            new VariableValueExtractor()
        );
    }

    public function parse(string $source): AssertionInterface
    {
        $source = trim($source);
        if ('' === $source) {
            return new Assertion($source, null, null);
        }

        $identifier = $this->findIdentifier($source);
        if ('' === $identifier) {
            return new Assertion($source, '', null);
        }

        $identifierLength = mb_strlen($identifier);
        $comparisonAndValue = trim(mb_substr($source, $identifierLength));

        $comparison = $this->findComparison($comparisonAndValue);
        if ('' === $comparison) {
            return new Assertion($source, $identifier, '');
        }

        $comparisonLength = strlen($comparison);
        $valueString = trim(mb_substr($comparisonAndValue, $comparisonLength));
        $value = $this->findValue($valueString);

        return new Assertion($source, $identifier, $comparison, $value);
    }

    private function findIdentifier(string $string): string
    {
        if ($this->literalValueExtractor->handles($string)) {
            return $this->literalValueExtractor->extract($string);
        }

        if ($this->pageElementIdentifierExtractor->handles($string)) {
            return $this->pageElementIdentifierExtractor->extract($string);
        }

        if ($this->variableValueExtractor->handles($string)) {
            return $this->variableValueExtractor->extract($string);
        }

        return '';
    }

    private function findComparison(string $source): ?string
    {
        $sourceLength = mb_strlen($source);

        foreach (self::COMPARISONS as $comparison) {
            $typeLength = strlen($comparison);

            if ($sourceLength >= $typeLength) {
                $sourcePrefix = mb_substr($source, 0, $typeLength);

                if ($sourcePrefix === $comparison) {
                    return $comparison;
                }
            }
        }

        return null;
    }

    private function findValue(string $valueString): ?string
    {
        if ($this->quotedValueExtractor->handles($valueString)) {
            return $this->quotedValueExtractor->extract($valueString);
        }

        if ($this->variableValueExtractor->handles($valueString)) {
            return $this->variableValueExtractor->extract($valueString);
        }

        return null;
    }
}
