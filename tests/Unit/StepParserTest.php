<?php

declare(strict_types=1);

namespace webignition\BasilParser\Tests\Unit;

use PHPUnit\Framework\TestCase;
use webignition\BasilDataStructure\Action\InteractionAction;
use webignition\BasilDataStructure\Assertion;
use webignition\BasilDataStructure\Step;
use webignition\BasilParser\StepParser;

class StepParserTest extends TestCase
{
    /**
     * @dataProvider parseDataProvider
     */
    public function testParse(array $stepData, Step $expectedStep)
    {
        $parser = StepParser::create();

        $this->assertEquals($expectedStep, $parser->parse($stepData));
    }

    public function parseDataProvider(): array
    {
        return [
            'empty' => [
                'stepData' => [],
                'expectedStep' => new Step([], []),
            ],
            'single action' => [
                'stepData' => [
                    'actions' => [
                        'click ".selector"',
                    ],
                ],
                'expectedStep' => new Step(
                    [
                        new InteractionAction(
                            'click ".selector"',
                            'click',
                            '".selector"',
                            '".selector"'
                        )
                    ],
                    []
                ),
            ],
            'single assertion' => [
                'stepData' => [
                    'assertions' => [
                        '".selector" exists',
                    ],
                ],
                'expectedStep' => new Step(
                    [],
                    [
                        new Assertion('".selector" exists', '".selector"', 'exists')
                    ]
                ),
            ],
        ];
    }
}
