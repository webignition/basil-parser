<?php

declare(strict_types=1);

namespace webignition\BasilParser;

use webignition\BasilDataStructure\Action\ActionInterface;
use webignition\BasilDataStructure\Step;

class StepParser
{
    private const KEY_ACTIONS = 'actions';
    private const KEY_ASSERTIONS = 'assertions';

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

    public function parse(array $stepData): Step
    {
        $actions = $this->parseActions($stepData[self::KEY_ACTIONS] ?? []);
        $assertions = $this->parseAssertions($stepData[self::KEY_ASSERTIONS] ?? []);

        return new Step($actions, $assertions);
    }

    /**
     * @param array $actionsData
     *
     * @return ActionInterface[]
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
     * @param array $assertionsData
     *
     * @return ActionInterface[]
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
}
