<?php

declare(strict_types=1);

namespace webignition\BasilParser\Test;

use webignition\BasilModels\Model\Step\StepCollection;
use webignition\BasilModels\Model\Test\Test;
use webignition\BasilModels\Model\Test\TestInterface;
use webignition\BasilParser\DataParserInterface;
use webignition\BasilParser\Exception\UnparseableStepException;
use webignition\BasilParser\Exception\UnparseableTestException;
use webignition\BasilParser\StepParser;

class TestParser implements DataParserInterface
{
    private const KEY_CONFIGURATION = 'config';
    private const KEY_BROWSER = 'browser';
    private const KEY_URL = 'url';
    private const KEY_IMPORTS = 'imports';

    public function __construct(
        private StepParser $stepParser
    ) {
    }

    public static function create(): TestParser
    {
        return new TestParser(
            StepParser::create()
        );
    }

    /**
     * @param array<mixed> $data
     *
     * @throws UnparseableTestException
     */
    public function parse(array $data): TestInterface
    {
        $configurationData = $data[self::KEY_CONFIGURATION] ?? [];
        $configurationData = is_array($configurationData) ? $configurationData : [];

        $browser = $configurationData[self::KEY_BROWSER] ?? '';
        $browser = is_string($browser) ? $browser : '';

        $url = $configurationData[self::KEY_URL] ?? '';
        $url = is_string($url) ? $url : '';

        $stepName = null;

        try {
            $stepNames = array_diff(array_keys($data), [self::KEY_CONFIGURATION, self::KEY_IMPORTS]);
            $steps = [];

            foreach ($stepNames as $stepName) {
                $stepData = $data[$stepName] ?? [];

                if (is_array($stepData)) {
                    $steps[$stepName] = $this->stepParser->parse($stepData);
                }
            }
        } catch (UnparseableStepException $unparseableStepException) {
            if (is_string($stepName)) {
                $unparseableStepException->setStepName($stepName);
            }

            throw new UnparseableTestException($data, $unparseableStepException);
        }

        return new Test($browser, $url, new StepCollection($steps));
    }
}
