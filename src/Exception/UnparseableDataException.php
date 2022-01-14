<?php

declare(strict_types=1);

namespace webignition\BasilParser\Exception;

class UnparseableDataException extends AbstractParserException implements UnparseableDataExceptionInterface
{
    /**
     * @param array<mixed> $data
     */
    public function __construct(
        private array $data,
        string $message,
        int $code,
        \Throwable $previous = null
    ) {
        parent::__construct(
            $message,
            $code,
            $previous
        );
    }

    public function getData(): array
    {
        return $this->data;
    }
}
