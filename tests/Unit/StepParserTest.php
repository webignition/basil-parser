<?php

declare(strict_types=1);

namespace webignition\BasilParser\Tests\Unit;

use PHPUnit\Framework\TestCase;
use webignition\BasilModels\Action\Action;
use webignition\BasilModels\Assertion\Assertion;
use webignition\BasilModels\DataSet\DataSetCollection;
use webignition\BasilModels\Step\Step;
use webignition\BasilModels\Step\StepInterface;
use webignition\BasilParser\Exception\UnparseableActionException;
use webignition\BasilParser\Exception\UnparseableAssertionException;
use webignition\BasilParser\Exception\UnparseableStepException;
use webignition\BasilParser\StepParser;

class StepParserTest extends TestCase
{
    private StepParser $parser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = StepParser::create();
    }

    /**
     * @dataProvider parseDataProvider
     *
     * @param array<mixed> $stepData
     * @param StepInterface $expectedStep
     */
    public function testParse(array $stepData, StepInterface $expectedStep)
    {
        $this->assertEquals($expectedStep, $this->parser->parse($stepData));
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
                        'click $".selector"',
                    ],
                ],
                'expectedStep' => new Step(
                    [
                        new Action(
                            'click $".selector"',
                            'click',
                            '$".selector"',
                            '$".selector"'
                        )
                    ],
                    []
                ),
            ],
            'single assertion' => [
                'stepData' => [
                    'assertions' => [
                        '$".selector" exists',
                    ],
                ],
                'expectedStep' => new Step(
                    [],
                    [
                        new Assertion('$".selector" exists', '$".selector"', 'exists')
                    ]
                ),
            ],
            'invalid import name; not a string' => [
                'stepData' => [
                    'use' => true,
                ],
                'expectedStep' => new Step([], []),
            ],
            'valid import name' => [
                'stepData' => [
                    'use' => 'import_name',
                ],
                'expectedStep' => (new Step([], []))->withImportName('import_name'),
            ],
            'invalid data import name; not a string' => [
                'stepData' => [
                    'data' => true,
                ],
                'expectedStep' => new Step([], []),
            ],
            'valid data import name' => [
                'stepData' => [
                    'data' => 'data_import_name',
                ],
                'expectedStep' => (new Step([], []))->withDataImportName('data_import_name'),
            ],
            'valid data array' => [
                'stepData' => [
                    'data' => [
                        'set1' => [
                            'key' => 'value',
                        ],
                    ],
                ],
                'expectedStep' => (new Step([], []))->withData(new DataSetCollection([
                    'set1' => [
                        'key' => 'value',
                    ],
                ])),
            ],
            'invalid elements; not an array' => [
                'stepData' => [
                    'elements' => 'string',
                ],
                'expectedStep' => new Step([], []),
            ],
            'valid elements' => [
                'stepData' => [
                    'elements' => [
                        'heading' => 'page_import_name.elements.heading',
                    ],
                ],
                'expectedStep' => (new Step([], []))->withIdentifiers([
                    'heading' => 'page_import_name.elements.heading',
                ]),
            ],
        ];
    }

    public function testParseStepWithEmptyAction()
    {
        try {
            $this->parser->parse([
                'actions' => [
                    '',
                ],
            ]);

            $this->fail('UnparseableStepException not thrown');
        } catch (UnparseableStepException $unparseableStepException) {
            $this->assertSame(
                [
                    'actions' => [
                        '',
                    ],
                ],
                $unparseableStepException->getData()
            );

            $this->assertEquals(
                UnparseableActionException::createEmptyActionException(),
                $unparseableStepException->getUnparseableStatementException()
            );
        }
    }

    public function testParseStepWithEmptyAssertion()
    {
        try {
            $this->parser->parse([
                'assertions' => [
                    '',
                ],
            ]);

            $this->fail('UnparseableStepException not thrown');
        } catch (UnparseableStepException $unparseableStepException) {
            $this->assertSame(
                [
                    'assertions' => [
                        '',
                    ],
                ],
                $unparseableStepException->getData()
            );

            $this->assertEquals(
                UnparseableAssertionException::createEmptyAssertionException(),
                $unparseableStepException->getUnparseableStatementException()
            );
        }
    }
}
