<?php

declare(strict_types=1);

namespace webignition\BasilParser\Exception;

class UnparseableTestException extends \Exception
{
    /**
     * @var array<mixed>
     */
    private $testData;
    private $unparseableStepException;

    /**
     * @param array<mixed> $testData
     * @param UnparseableStepException $unparseableStepException
     */
    public function __construct(
        array $testData,
        UnparseableStepException $unparseableStepException
    ) {
        parent::__construct(
            'Unparseable test',
            0,
            $unparseableStepException
        );

        $this->testData = $testData;
        $this->unparseableStepException = $unparseableStepException;
    }

    /**
     * @return array<mixed>
     */
    public function getTestData(): array
    {
        return $this->testData;
    }

    public function getUnparseableStepException(): UnparseableStepException
    {
        return $this->unparseableStepException;
    }
}
