<?php

declare(strict_types=1);

namespace webignition\BasilParser\Exception;

class EmptyInputActionValueException extends InvalidActionException
{
    public function __construct(string $source)
    {
        parent::__construct($source, 'Empty input value in action "' . $source . '"');
    }
}
