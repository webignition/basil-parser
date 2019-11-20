<?php

declare(strict_types=1);

namespace webignition\BasilParser;

use webignition\BasilDataStructure\Assertion;
use webignition\BasilDataStructure\AssertionInterface;
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
    private $variableValueExtractor;
    private $identifierExtractor;

    public function __construct(
        QuotedValueExtractor $quotedValueExtractor,
        VariableValueExtractor $variableValueExtractor,
        IdentifierExtractor $identifierExtractor
    ) {
        $this->quotedValueExtractor = $quotedValueExtractor;
        $this->variableValueExtractor = $variableValueExtractor;
        $this->identifierExtractor = $identifierExtractor;
    }

    public static function create(): AssertionParser
    {
        return new AssertionParser(
            new QuotedValueExtractor(),
            new VariableValueExtractor(),
            IdentifierExtractor::create()
        );
    }

    public function parse(string $source): AssertionInterface
    {
        $source = trim($source);
        if ('' === $source) {
            return new Assertion($source, null, null);
        }

        $identifier = $this->identifierExtractor->extract($source);
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

    private function findComparison(string $source): string
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

        return '';
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
