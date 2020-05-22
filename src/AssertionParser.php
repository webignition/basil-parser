<?php

declare(strict_types=1);

namespace webignition\BasilParser;

use webignition\BasilModels\Assertion\Assertion;
use webignition\BasilModels\Assertion\AssertionInterface;
use webignition\BasilModels\Assertion\ComparisonAssertion;
use webignition\BasilParser\Exception\UnparseableAssertionException;
use webignition\BasilValueExtractor\ValueExtractor;

class AssertionParser
{
    private const COMPARISON_REGEX = '/^[a-z\-]+ ?/';

    private const COMPARISON_ASSERTIONS = [
        'excludes',
        'includes',
        'is-not',
        'is',
        'matches',
    ];

    private ValueExtractor $valueExtractor;

    public function __construct(ValueExtractor $valueExtractor)
    {
        $this->valueExtractor = $valueExtractor;
    }

    public static function create(): AssertionParser
    {
        return new AssertionParser(
            ValueExtractor::createExtractor()
        );
    }

    /**
     * @param string $source
     *
     * @return AssertionInterface
     *
     * @throws UnparseableAssertionException
     */
    public function parse(string $source): AssertionInterface
    {
        $source = trim($source);
        if ('' === $source) {
            throw UnparseableAssertionException::createEmptyAssertionException();
        }

        $identifier = $this->valueExtractor->extract($source);
        if (null === $identifier) {
            throw UnparseableAssertionException::createEmptyIdentifierException($source);
        }

        $identifierLength = mb_strlen($identifier);
        $comparisonAndValue = trim(mb_substr($source, $identifierLength));

        $comparison = $this->findComparison($comparisonAndValue);
        if (null === $comparison) {
            throw UnparseableAssertionException::createEmptyComparisonException($source);
        }

        if (!in_array($comparison, self::COMPARISON_ASSERTIONS)) {
            return new Assertion($source, $identifier, $comparison);
        }

        $comparisonLength = strlen($comparison);
        $valueString = trim(mb_substr($comparisonAndValue, $comparisonLength));

        if ('' === $valueString) {
            throw UnparseableAssertionException::createEmptyValueException($source);
        }

        $value = $this->valueExtractor->extract($valueString);

        if (null === $value) {
            throw UnparseableAssertionException::createInvalidValueFormatException($source);
        }

        return new ComparisonAssertion($source, $identifier, $comparison, $value);
    }

    private function findComparison(string $sourceAndValue): ?string
    {
        $comparisonMatches = [];
        preg_match(self::COMPARISON_REGEX, $sourceAndValue, $comparisonMatches);

        if (0 === count($comparisonMatches)) {
            return null;
        }

        return trim($comparisonMatches[0]);
    }
}
