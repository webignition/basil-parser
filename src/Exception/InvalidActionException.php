<?php

declare(strict_types=1);

namespace webignition\BasilParser\Exception;

class InvalidActionException extends \Exception
{
    private $source;

    public function __construct(string $source, string $message)
    {
        parent::__construct($message);

        $this->source = $source;
    }

    public function getSource(): string
    {
        return $this->source;
    }
}
