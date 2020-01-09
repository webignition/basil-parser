<?php

declare(strict_types=1);

namespace webignition\BasilParser\Exception;

class UnparseableAssertionException extends \Exception
{
    public const CODE_EMPTY = 1;
    public const CODE_EMPTY_COMPARISON = 2;
    public const CODE_EMPTY_IDENTIFIER = 3;
    public const CODE_EMPTY_VALUE = 4;

    private $assertionString;

    private function __construct(string $assertionString, int $code)
    {
        parent::__construct(sprintf('Unparseable assertion "%s"', $assertionString), $code);

        $this->assertionString = $assertionString;
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

    public function getAssertionString(): string
    {
        return $this->assertionString;
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
