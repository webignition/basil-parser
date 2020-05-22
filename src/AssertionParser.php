<?php

declare(strict_types=1);

namespace webignition\BasilParser;

use webignition\BasilModels\Assertion\Assertion;
use webignition\BasilModels\Assertion\AssertionInterface;
use webignition\BasilParser\Exception\UnparseableAssertionException;
use webignition\BasilValueExtractor\ValueExtractor;

class AssertionParser
{
    private const OPERATOR_REGEX = '/^[a-z\-]+ ?/';

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
        $operatorAndValue = trim(mb_substr($source, $identifierLength));

        $operator = $this->findOperator($operatorAndValue);
        if (null === $operator) {
            throw UnparseableAssertionException::createEmptyComparisonException($source);
        }

        if (!in_array($operator, ['excludes', 'includes', 'is-not', 'is', 'matches',])) {
            return new Assertion($source, $identifier, $operator);
        }

        $operatorLength = strlen($operator);
        $valueString = trim(mb_substr($operatorAndValue, $operatorLength));

        if ('' === $valueString) {
            throw UnparseableAssertionException::createEmptyValueException($source);
        }

        $value = $this->valueExtractor->extract($valueString);

        if (null === $value) {
            throw UnparseableAssertionException::createInvalidValueFormatException($source);
        }

        return new Assertion($source, $identifier, $operator, $value);
    }

    private function findOperator(string $sourceAndValue): ?string
    {
        $operatorMatches = [];
        preg_match(self::OPERATOR_REGEX, $sourceAndValue, $operatorMatches);

        if (0 === count($operatorMatches)) {
            return null;
        }

        return trim($operatorMatches[0]);
    }
}
