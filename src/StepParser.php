<?php

declare(strict_types=1);

namespace webignition\BasilParser;

use webignition\BasilModels\Action\ActionInterface;
use webignition\BasilModels\Assertion\AssertionInterface;
use webignition\BasilModels\DataSet\DataSetCollection;
use webignition\BasilModels\Step\Step;
use webignition\BasilModels\Step\StepInterface;
use webignition\BasilParser\Exception\EmptyActionException;
use webignition\BasilParser\Exception\EmptyAssertionComparisonException;
use webignition\BasilParser\Exception\EmptyAssertionException;
use webignition\BasilParser\Exception\EmptyAssertionIdentifierException;
use webignition\BasilParser\Exception\EmptyAssertionValueException;
use webignition\BasilParser\Exception\EmptyInputActionValueException;

class StepParser
{
    private const KEY_ACTIONS = 'actions';
    private const KEY_ASSERTIONS = 'assertions';
    private const KEY_IMPORT_NAME = 'use';
    private const KEY_DATA = 'data';
    private const KEY_ELEMENTS = 'elements';

    private $actionParser;
    private $assertionParser;

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
     * @param array<mixed> $stepData
     *
     * @return StepInterface
     *
     * @throws EmptyActionException
     * @throws EmptyAssertionComparisonException
     * @throws EmptyAssertionException
     * @throws EmptyAssertionIdentifierException
     * @throws EmptyInputActionValueException
     * @throws EmptyAssertionValueException
     */
    public function parse(array $stepData): StepInterface
    {
        $actions = $this->parseActions($stepData[self::KEY_ACTIONS] ?? []);
        $assertions = $this->parseAssertions($stepData[self::KEY_ASSERTIONS] ?? []);

        $step = new Step($actions, $assertions);
        $step = $this->setImportName($step, $stepData[self::KEY_IMPORT_NAME] ?? null);
        $step = $this->setData($step, $stepData[self::KEY_DATA] ?? null);
        $step = $this->setIdentifiers($step, $stepData[self::KEY_ELEMENTS] ?? null);


        return $step;
    }

    /**
     * @param array<mixed> $actionsData
     *
     * @return ActionInterface[]
     *
     * @throws EmptyActionException
     * @throws EmptyInputActionValueException
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
     * @throws EmptyAssertionComparisonException
     * @throws EmptyAssertionException
     * @throws EmptyAssertionIdentifierException
     * @throws EmptyAssertionValueException
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
