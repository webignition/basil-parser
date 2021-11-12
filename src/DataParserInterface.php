<?php

declare(strict_types=1);

namespace webignition\BasilParser;

use webignition\BasilParser\Exception\UnparseableDataExceptionInterface;

interface DataParserInterface
{
    /**
     * @param array<mixed> $data
     *
     * @throws UnparseableDataExceptionInterface
     *
     * @return mixed
     */
    public function parse(array $data);
}
