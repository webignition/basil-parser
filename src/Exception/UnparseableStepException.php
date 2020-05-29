<?php

declare(strict_types=1);

namespace webignition\BasilParser\Exception;

class UnparseableStepException extends UnparseableDataException
{
    public const CODE_UNPARSEABLE_ACTION = 1;
    public const CODE_UNPARSEABLE_ASSERTION = 2;
    public const CODE_INVALID_ACTIONS_DATA = 3;

    private ?UnparseableStatementException $unparseableStatementException = null;
    private ?string $stepName;

    /**
     * @param array<mixed> $stepData
     * @param int $code
     * @param UnparseableStatementException $unparseableStatementException
     */
    private function __construct(
        array $stepData,
        int $code,
        ?UnparseableStatementException $unparseableStatementException = null
    ) {
        parent::__construct($stepData, 'Unparseable step', $code, $unparseableStatementException);

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
     * @param array<mixed> $stepData
     *
     * @return UnparseableStepException
     */
    public static function createForInvalidActionsData(array $stepData)
    {
        return new UnparseableStepException(
            $stepData,
            self::CODE_INVALID_ACTIONS_DATA
        );
    }

    public function getUnparseableStatementException(): ?UnparseableStatementException
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

    public function getStepName(): ?string
    {
        return $this->stepName;
    }

    public function setStepName(string $stepName): void
    {
        $this->stepName = $stepName;
    }
}
