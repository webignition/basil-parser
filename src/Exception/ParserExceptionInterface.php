<?php

declare(strict_types=1);

namespace webignition\BasilParser\Exception;

interface ParserExceptionInterface extends \Throwable
{
    public function getIntCode(): int;
}
