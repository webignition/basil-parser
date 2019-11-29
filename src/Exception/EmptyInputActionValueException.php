<?php

declare(strict_types=1);

namespace webignition\BasilParser\Exception;

class EmptyInputActionValueException extends \Exception
{
    private $source;

    public function __construct(string $source)
    {
        parent::__construct('Empty input value in action "' . $source . '"');

        $this->source = $source;
    }

    public function getSource(): string
    {
        return $this->source;
    }
}
