<?php

declare(strict_types=1);

namespace webignition\BasilParser\Exception;

interface UnparseableDataExceptionInterface extends \Throwable
{
    /**
     * @return array<mixed>
     */
    public function getData(): array;
}
