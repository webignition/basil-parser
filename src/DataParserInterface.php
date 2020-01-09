<?php

declare(strict_types=1);

namespace webignition\BasilParser;

use webignition\BasilParser\Exception\UnparseableDataExceptionInterface;

interface DataParserInterface
{
    /**
     * @param array<mixed> $data
     *
     * @return mixed
     *
     * @throws UnparseableDataExceptionInterface
     */
    public function parse(array $data);
}
