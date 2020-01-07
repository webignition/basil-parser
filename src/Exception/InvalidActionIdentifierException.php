<?php

declare(strict_types=1);

namespace webignition\BasilParser\Exception;

class InvalidActionIdentifierException extends InvalidActionException
{
    public function __construct(string $source)
    {
        parent::__construct($source, 'Invalid identifier in action "' . $source . '"');
    }
}
