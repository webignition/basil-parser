<?php

declare(strict_types=1);

namespace webignition\BasilParser\Exception;

class UnparseableTestException extends UnparseableDataException
{
    private UnparseableStepException $unparseableStepException;

    /**
     * @param array<mixed> $testData
     */
    public function __construct(
        array $testData,
        UnparseableStepException $unparseableStepException
    ) {
        parent::__construct(
            $testData,
            'Unparseable test',
            0,
            $unparseableStepException
        );

        $this->unparseableStepException = $unparseableStepException;
    }

    public function getUnparseableStepException(): UnparseableStepException
    {
        return $this->unparseableStepException;
    }
}
