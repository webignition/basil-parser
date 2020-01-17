<?php

declare(strict_types=1);

namespace webignition\BasilParser\Exception;

class UnparseableAssertionException extends UnparseableStatementException
{
    public const CODE_EMPTY = 1;
    public const CODE_EMPTY_COMPARISON = 2;
    public const CODE_EMPTY_IDENTIFIER = 3;
    public const CODE_EMPTY_VALUE = 4;
    public const CODE_INVALID_VALUE_FORMAT = 5;

    private function __construct(string $assertionString, int $code)
    {
        parent::__construct($assertionString, sprintf('Unparseable assertion "%s"', $assertionString), $code);
    }

    public static function createEmptyAssertionException(): UnparseableAssertionException
    {
        return new UnparseableAssertionException('', self::CODE_EMPTY);
    }

    public static function createEmptyComparisonException(string $assertionString): UnparseableAssertionException
    {
        return new UnparseableAssertionException($assertionString, self::CODE_EMPTY_COMPARISON);
    }

    public static function createEmptyIdentifierException(string $assertionString): UnparseableAssertionException
    {
        return new UnparseableAssertionException($assertionString, self::CODE_EMPTY_IDENTIFIER);
    }

    public static function createEmptyValueException(string $assertionString): UnparseableAssertionException
    {
        return new UnparseableAssertionException($assertionString, self::CODE_EMPTY_VALUE);
    }

    public static function createInvalidValueFormatException(string $assertionString): UnparseableAssertionException
    {
        return new UnparseableAssertionException($assertionString, self::CODE_INVALID_VALUE_FORMAT);
    }

    public function isEmptyAssertionException(): bool
    {
        return self::CODE_EMPTY === $this->code;
    }

    public function isEmptyComparisonException(): bool
    {
        return self::CODE_EMPTY_COMPARISON === $this->code;
    }

    public function isEmptyIdentifierException(): bool
    {
        return self::CODE_EMPTY_IDENTIFIER === $this->code;
    }

    public function isEmptyValueException(): bool
    {
        return self::CODE_EMPTY_VALUE === $this->code;
    }
}
