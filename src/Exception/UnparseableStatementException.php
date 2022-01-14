<?php

declare(strict_types=1);

namespace webignition\BasilParser\Exception;

class UnparseableStatementException extends AbstractParserException
{
    protected function __construct(
        private string $statement,
        string $message,
        int $code
    ) {
        parent::__construct($message, $code);
    }

    public function getStatement(): string
    {
        return $this->statement;
    }
}
