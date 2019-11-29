<?php

declare(strict_types=1);

namespace webignition\BasilParser\Exception;

class EmptyAssertionValueException extends \Exception
{
    private $value;

    public function __construct(string $source)
    {
        parent::__construct('Empty value in assertion "' . $source . '"');

        $this->value = $source;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
