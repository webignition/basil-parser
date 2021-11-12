<?php

declare(strict_types=1);

namespace webignition\BasilParser\Exception;

class UnparseableStatementException extends AbstractParserException
{
    private string $statement;

    protected function __construct(string $statement, string $message, int $code)
    {
        parent::__construct($message, $code);

        $this->statement = $statement;
    }

    public function getStatement(): string
    {
        return $this->statement;
    }
}
