<?php

declare(strict_types=1);

namespace webignition\BasilParser\Exception;

class UnparseableDataException extends \Exception implements UnparseableDataExceptionInterface
{
    /**
     * @var array<mixed>
     */
    private array $data;

    /**
     * @param array<mixed> $data
     */
    public function __construct(array $data, string $message, int $code, \Throwable $previous = null)
    {
        parent::__construct(
            $message,
            $code,
            $previous
        );

        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
