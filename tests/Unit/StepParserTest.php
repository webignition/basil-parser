<?php

declare(strict_types=1);

namespace webignition\BasilParser\Tests\Unit;

use PHPUnit\Framework\TestCase;
use webignition\BasilModels\Model\Action\Action;
use webignition\BasilModels\Model\Assertion\Assertion;
use webignition\BasilModels\Model\DataSet\DataSetCollection;
use webignition\BasilModels\Model\Step\Step;
use webignition\BasilModels\Model\Step\StepInterface;
use webignition\BasilParser\Exception\UnparseableActionException;
use webignition\BasilParser\Exception\UnparseableAssertionException;
use webignition\BasilParser\Exception\UnparseableStatementException;
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
     */
    public function testParse(array $stepData, StepInterface $expectedStep): void
    {
        $this->assertEquals($expectedStep, $this->parser->parse($stepData));
    }

    /**
     * @return array<mixed>
     */
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

    /**
     * @dataProvider throwsUnparseableStepExceptionDataProvider
     *
     * @param array<mixed> $stepData
     */
    public function testThrowsUnparseableStepException(
        array $stepData,
        ?UnparseableStatementException $expectedStatementException
    ): void {
        try {
            $this->parser->parse($stepData);

            $this->fail('UnparseableStepException not thrown');
        } catch (UnparseableStepException $unparseableStepException) {
            $this->assertSame($stepData, $unparseableStepException->getData());
            $this->assertEquals(
                $expectedStatementException,
                $unparseableStepException->getUnparseableStatementException()
            );
        }
    }

    /**
     * @return array<mixed>
     */
    public function throwsUnparseableStepExceptionDataProvider(): array
    {
        return [
            'empty action' => [
                'stepData' => [
                    'actions' => [
                        '',
                    ],
                ],
                'expectedStatementException' => UnparseableActionException::createEmptyActionException(),
            ],
            'empty assertion' => [
                'stepData' => [
                    'assertions' => [
                        '',
                    ],
                ],
                'expectedStatementException' => UnparseableAssertionException::createEmptyAssertionException(),
            ],
            'invalid actions data' => [
                'stepData' => [
                    'actions' => 'not an array',
                ],
                'expectedStatementException' => null,
            ],
            'invalid assertions data' => [
                'stepData' => [
                    'assertions' => 'not an array',
                ],
                'expectedStatementException' => null,
            ],
        ];
    }
}
