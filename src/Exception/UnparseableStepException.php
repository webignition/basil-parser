<?php

declare(strict_types=1);

namespace webignition\BasilParser\Exception;

use webignition\BasilModels\Step\StepInterface;

class UnparseableStepException extends \Exception
{
    public const CODE_UNPARSEABLE_ACTION = 1;
    public const CODE_UNPARSEABLE_ASSERTION = 2;

    private $stepData;
    private $unparseableStatementException;

    /**
     * @param array<mixed> $stepData
     * @param int $code
     * @param UnparseableStatementException $unparseableStatementException
     */
    public function __construct(
        array $stepData,
        int $code,
        UnparseableStatementException $unparseableStatementException
    ) {
        parent::__construct('Unparseable step', $code, $unparseableStatementException);

        $this->stepData = $stepData;
        $this->unparseableStatementException = $unparseableStatementException;
    }

    /**
     * @param array<mixed> $stepData
     * @param UnparseableActionException $unparseableActionException
     *
     * @return UnparseableStepException
     */
    public static function createForUnparseableActionException(
        array $stepData,
        UnparseableActionException $unparseableActionException
    ): UnparseableStepException {
        return new UnparseableStepException($stepData, self::CODE_UNPARSEABLE_ACTION, $unparseableActionException);
    }

    /**
     * @param array<mixed> $stepData
     * @param UnparseableAssertionException $unparseableAssertionException
     *
     * @return UnparseableStepException
     */
    public static function createForUnparseableAssertionException(
        array $stepData,
        UnparseableAssertionException $unparseableAssertionException
    ): UnparseableStepException {
        return new UnparseableStepException(
            $stepData,
            self::CODE_UNPARSEABLE_ASSERTION,
            $unparseableAssertionException
        );
    }

    /**
     * @return array<mixed>
     */
    public function getStepData(): array
    {
        return $this->stepData;
    }

    public function getUnparseableStatementException(): UnparseableStatementException
    {
        return $this->unparseableStatementException;
    }

    public function isForUnparseableActionException(): bool
    {
        return self::CODE_UNPARSEABLE_ACTION === $this->code;
    }

    public function isForUnparseableAssertionException(): bool
    {
        return self::CODE_UNPARSEABLE_ASSERTION === $this->code;
    }
}
