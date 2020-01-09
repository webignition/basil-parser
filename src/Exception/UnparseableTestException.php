<?php

declare(strict_types=1);

namespace webignition\BasilParser\Exception;

class UnparseableTestException extends \Exception
{
    private $basePath;
    private $name;

    /**
     * @var array<mixed>
     */
    private $testData;
    private $unparseableStepException;

    /**
     * @param string $basePath
     * @param string $name
     * @param array<mixed> $testData
     * @param UnparseableStepException $unparseableStepException
     */
    public function __construct(
        string $basePath,
        string $name,
        array $testData,
        UnparseableStepException $unparseableStepException
    ) {
        parent::__construct(
            'Unparseable test',
            0,
            $unparseableStepException
        );

        $this->basePath = $basePath;
        $this->name = $name;
        $this->testData = $testData;
        $this->unparseableStepException = $unparseableStepException;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function getName(): string
    {
        return $this->name;
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
