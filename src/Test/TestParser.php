<?php

declare(strict_types=1);

namespace webignition\BasilParser\Test;

use webignition\BasilDataStructure\Step;
use webignition\BasilDataStructure\Test\Test;
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

    public function parse(string $basePath, string $name, array $testData): Test
    {
        $test = new Test(
            $name,
            $this->configurationParser->parse($testData[self::KEY_CONFIGURATION] ?? []),
            $this->getSteps($testData)
        );

        $imports = $this->importsParser->parse($basePath, $testData[self::KEY_IMPORTS] ?? []);
        $test = $test->withImports($imports);

        return $test;
    }

    /**
     * @param array $testData
     *
     * @return Step[]
     */
    public function getSteps(array $testData): array
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
