<?php

declare(strict_types=1);

namespace webignition\BasilParser\Test;

use webignition\BasilModels\Step\StepInterface;
use webignition\BasilModels\Test\Test;
use webignition\BasilModels\Test\TestInterface;
use webignition\BasilParser\Exception\UnparseableStepException;
use webignition\BasilParser\Exception\UnparseableTestException;
use webignition\BasilParser\StepParser;

class TestParser
{
    private const KEY_CONFIGURATION = 'config';
    private const KEY_IMPORTS = 'imports';

    private $configurationParser;
    private $importsParser;
    private $stepParser;

    public function __construct(
        ConfigurationParser $configurationParser,
        ImportsParser $importsParser,
        StepParser $stepParser
    ) {
        $this->configurationParser = $configurationParser;
        $this->importsParser = $importsParser;
        $this->stepParser = $stepParser;
    }

    public static function create(): TestParser
    {
        return new TestParser(
            ConfigurationParser::create(),
            ImportsParser::create(),
            StepParser::create()
        );
    }

    /**
     * @param string $basePath
     * @param string $name
     * @param array<mixed> $testData
     *
     * @return TestInterface
     *
     * @throws UnparseableTestException
     */
    public function parse(string $basePath, string $name, array $testData): TestInterface
    {
        $imports = $this->importsParser->parse($basePath, $testData[self::KEY_IMPORTS] ?? []);

        $configuration = $this->configurationParser->parse($testData[self::KEY_CONFIGURATION] ?? []);

        try {
            $steps = $this->getSteps($testData);
        } catch (UnparseableStepException $unparseableStepException) {
            throw new UnparseableTestException($basePath, $name, $testData, $unparseableStepException);
        }

        return new Test($name, $configuration, $steps, $imports);
    }

    /**
     * @param array<mixed> $testData
     *
     * @return StepInterface[]
     *
     * @throws UnparseableStepException
     */
    private function getSteps(array $testData): array
    {
        $stepNames = array_diff(array_keys($testData), [self::KEY_CONFIGURATION, self::KEY_IMPORTS]);

        $steps = [];

        foreach ($stepNames as $stepName) {
            $stepData = $testData[$stepName] ?? [];

            if (is_array($stepData)) {
                $steps[$stepName] = $this->stepParser->parse($stepData);
            }
        }

        return $steps;
    }
}
