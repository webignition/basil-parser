<?php

declare(strict_types=1);

namespace webignition\BasilParser\Exception;

class EmptyAssertionIdentifierException extends \Exception
{
    private $identifier;

    public function __construct(string $source)
    {
        parent::__construct('Empty identifier in assertion "' . $source . '"');

        $this->identifier = $source;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}
