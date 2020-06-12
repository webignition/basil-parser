<?php

declare(strict_types=1);

namespace webignition\BasilParser\Test;

use webignition\BasilModels\Step\StepCollection;
use webignition\BasilModels\Test\Test;
use webignition\BasilModels\Test\TestInterface;
use webignition\BasilParser\DataParserInterface;
use webignition\BasilParser\Exception\UnparseableStepException;
use webignition\BasilParser\Exception\UnparseableTestException;
use webignition\BasilParser\StepParser;

class TestParser implements DataParserInterface
{
    private const KEY_CONFIGURATION = 'config';
    private const KEY_IMPORTS = 'imports';

    private ConfigurationParser $configurationParser;
    private StepParser $stepParser;

    public function __construct(ConfigurationParser $configurationParser, StepParser $stepParser)
    {
        $this->configurationParser = $configurationParser;
        $this->stepParser = $stepParser;
    }

    public static function create(): TestParser
    {
        return new TestParser(
            ConfigurationParser::create(),
            StepParser::create()
        );
    }

    /**
     * @param array<mixed> $testData
     *
     * @return TestInterface
     *
     * @throws UnparseableTestException
     */
    public function parse(array $testData): TestInterface
    {
        $configuration = $this->configurationParser->parse($testData[self::KEY_CONFIGURATION] ?? []);

        $stepName = null;

        try {
            $stepNames = array_diff(array_keys($testData), [self::KEY_CONFIGURATION, self::KEY_IMPORTS]);
            $steps = [];

            foreach ($stepNames as $stepName) {
                $stepData = $testData[$stepName] ?? [];

                if (is_array($stepData)) {
                    $steps[$stepName] = $this->stepParser->parse($stepData);
                }
            }
        } catch (UnparseableStepException $unparseableStepException) {
            if (is_string($stepName)) {
                $unparseableStepException->setStepName($stepName);
            }

            throw new UnparseableTestException($testData, $unparseableStepException);
        }

        return new Test($configuration, new StepCollection($steps));
    }
}
