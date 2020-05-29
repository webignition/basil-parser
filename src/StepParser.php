<?php

declare(strict_types=1);

namespace webignition\BasilParser;

use webignition\BasilModels\Action\ActionInterface;
use webignition\BasilModels\Assertion\AssertionInterface;
use webignition\BasilModels\DataSet\DataSetCollection;
use webignition\BasilModels\Step\Step;
use webignition\BasilModels\Step\StepInterface;
use webignition\BasilParser\Exception\UnparseableActionException;
use webignition\BasilParser\Exception\UnparseableAssertionException;
use webignition\BasilParser\Exception\UnparseableStepException;

class StepParser implements DataParserInterface
{
    private const KEY_ACTIONS = 'actions';
    private const KEY_ASSERTIONS = 'assertions';
    private const KEY_IMPORT_NAME = 'use';
    private const KEY_DATA = 'data';
    private const KEY_ELEMENTS = 'elements';

    private ActionParser $actionParser;
    private AssertionParser $assertionParser;

    public function __construct(ActionParser $actionParser, AssertionParser $assertionParser)
    {
        $this->actionParser = $actionParser;
        $this->assertionParser = $assertionParser;
    }

    public static function create(): StepParser
    {
        return new StepParser(
            ActionParser::create(),
            AssertionParser::create()
        );
    }

    /**
     * @param array<mixed> $data
     *
     * @return StepInterface
     *
     * @throws UnparseableStepException
     */
    public function parse(array $data): StepInterface
    {
        $actionsData = $data[self::KEY_ACTIONS] ?? [];
        if (!is_array($actionsData)) {
            throw UnparseableStepException::createForInvalidActionsData($data);
        }

        try {
            $actions = $this->parseActions($data[self::KEY_ACTIONS] ?? []);
        } catch (UnparseableActionException $unparseableActionException) {
            throw UnparseableStepException::createForUnparseableActionException($data, $unparseableActionException);
        }

        try {
            $assertions = $this->parseAssertions($data[self::KEY_ASSERTIONS] ?? []);
        } catch (UnparseableAssertionException $unparseableAssertionException) {
            throw UnparseableStepException::createForUnparseableAssertionException(
                $data,
                $unparseableAssertionException
            );
        }

        $step = new Step($actions, $assertions);
        $step = $this->setImportName($step, $data[self::KEY_IMPORT_NAME] ?? null);
        $step = $this->setData($step, $data[self::KEY_DATA] ?? null);
        $step = $this->setIdentifiers($step, $data[self::KEY_ELEMENTS] ?? null);


        return $step;
    }

    /**
     * @param array<mixed> $actionsData
     *
     * @return ActionInterface[]
     *
     * @throws UnparseableActionException
     */
    private function parseActions(array $actionsData): array
    {
        $actions = [];

        foreach ($actionsData as $actionString) {
            if (is_string($actionString)) {
                $actions[] = $this->actionParser->parse($actionString);
            }
        }

        return $actions;
    }

    /**
     * @param array<mixed> $assertionsData
     *
     * @return AssertionInterface[]
     *
     * @throws UnparseableAssertionException
     */
    private function parseAssertions(array $assertionsData): array
    {
        $assertions = [];

        foreach ($assertionsData as $assertionString) {
            if (is_string($assertionString)) {
                $assertions[] = $this->assertionParser->parse($assertionString);
            }
        }

        return $assertions;
    }

    /**
     * @param StepInterface $step
     * @param mixed $importName
     *
     * @return StepInterface
     */
    private function setImportName(StepInterface $step, $importName): StepInterface
    {
        if (!is_string($importName)) {
            $importName = null;
        }

        if (is_string($importName)) {
            $step = $step->withImportName($importName);
        }

        return $step;
    }

    /**
     * @param StepInterface $step
     * @param mixed $data
     *
     * @return StepInterface
     */
    private function setData(StepInterface $step, $data): StepInterface
    {
        if (is_array($data)) {
            $step = $step->withData(new DataSetCollection($data));
        }

        if (is_string($data)) {
            $step = $step->withDataImportName($data);
        }

        return $step;
    }

    /**
     * @param StepInterface $step
     * @param mixed $identifiers
     *
     * @return StepInterface
     */
    private function setIdentifiers(StepInterface $step, $identifiers): StepInterface
    {
        if (is_array($identifiers)) {
            $step = $step->withIdentifiers($identifiers);
        }

        return $step;
    }
}
