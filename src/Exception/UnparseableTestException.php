<?php

declare(strict_types=1);

namespace webignition\BasilParser\Exception;

class UnparseableTestException extends UnparseableDataException
{
    /**
     * @param array<mixed> $testData
     */
    public function __construct(
        array $testData,
        private UnparseableStepException $unparseableStepException
    ) {
        parent::__construct(
            $testData,
            'Unparseable test',
            0,
            $unparseableStepException
        );
    }

    public function getUnparseableStepException(): UnparseableStepException
    {
        return $this->unparseableStepException;
    }
}
