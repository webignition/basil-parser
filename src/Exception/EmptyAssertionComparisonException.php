<?php

declare(strict_types=1);

namespace webignition\BasilParser\Exception;

class EmptyAssertionComparisonException extends \Exception
{
    private $comparison;

    public function __construct(string $source)
    {
        parent::__construct('Empty comparison in assertion "' . $source . '"');

        $this->comparison = $source;
    }

    public function getComparison(): string
    {
        return $this->comparison;
    }
}
