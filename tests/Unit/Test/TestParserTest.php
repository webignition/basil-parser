<?php

declare(strict_types=1);

namespace webignition\BasilParser\Tests\Unit\Test;

use PHPUnit\Framework\TestCase;
use webignition\BasilModels\Model\Action\Action;
use webignition\BasilModels\Model\Assertion\Assertion;
use webignition\BasilModels\Model\DataSet\DataSetCollection;
use webignition\BasilModels\Model\Step\Step;
use webignition\BasilModels\Model\Step\StepCollection;
use webignition\BasilModels\Model\Test\Test;
use webignition\BasilModels\Model\Test\TestInterface;
use webignition\BasilParser\Exception\UnparseableActionException;
use webignition\BasilParser\Exception\UnparseableStepException;
use webignition\BasilParser\Exception\UnparseableTestException;
use webignition\BasilParser\Test\TestParser;

class TestParserTest extends TestCase
{
    private TestParser $parser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = TestParser::create();
    }

    /**
     * @dataProvider parseDataProvider
     *
     * @param array<mixed> $testData
     */
    public function testParse(array $testData, TestInterface $expectedTest): void
    {
        $this->assertEquals($expectedTest, $this->parser->parse($testData));
    }

    /**
     * @return array<mixed>
     */
    public function parseDataProvider(): array
    {
        return [
            'empty' => [
                'testData' => [],
                'expectedTest' => new Test(
                    '',
                    '',
                    new StepCollection([])
                ),
            ],
            'non-empty' => [
                'testData' => [
                    'config' => [
                        'browser' => 'chrome',
                        'url' => 'http://example.com/',
                    ],
                    'imports' => [
                        'steps' => [
                            'step_import_name' => 'step/one.yml',
                        ],
                        'data_providers' => [
                            'data_provider_import_name' => 'data/data.yml',
                        ],
                        'pages' => [
                            'page_import_name' => 'page/page.yml',
                        ],
                    ],
                    'step one' => [
                        'use' => 'step_import_name',
                        'data' => [
                            'set1' => [
                                'key1' => 'value1',
                            ],
                        ],
                    ],
                    'step two' => [
                        'data' => 'data_provider_import_name',
                        'actions' => [
                            'click $page_import_name.elements.button',
                        ],
                        'assertions' => [
                            '$page.title is $data.expected_title'
                        ],
                    ],
                ],
                'expectedTest' => new Test(
                    'chrome',
                    'http://example.com/',
                    new StepCollection([
                        'step one' => (new Step([], []))
                            ->withImportName('step_import_name')
                            ->withData(new DataSetCollection([
                                'set1' => [
                                    'key1' => 'value1',
                                ],
                            ])),
                        'step two' => (new Step(
                            [
                                new Action(
                                    'click $page_import_name.elements.button',
                                    'click',
                                    '$page_import_name.elements.button',
                                    '$page_import_name.elements.button'
                                )
                            ],
                            [
                                new Assertion(
                                    '$page.title is $data.expected_title',
                                    '$page.title',
                                    'is',
                                    '$data.expected_title'
                                )
                            ]
                        ))->withDataImportName('data_provider_import_name'),
                    ])
                ),
            ],
        ];
    }

    public function testParseTestWithStepWithEmptyAction(): void
    {
        $testData = [
            'step name' => [
                'actions' => [
                    '',
                ],
            ],
        ];

        try {
            $this->parser->parse($testData);

            $this->fail('UnparseableTestException not thrown');
        } catch (UnparseableTestException $unparseableTestException) {
            $this->assertSame($testData, $unparseableTestException->getData());

            $expectedUnparseableStepException = UnparseableStepException::createForUnparseableAction(
                [
                    'actions' => [
                        '',
                    ],
                ],
                UnparseableActionException::createEmptyActionException()
            );

            $expectedUnparseableStepException->setStepName('step name');

            $this->assertEquals(
                $expectedUnparseableStepException,
                $unparseableTestException->getUnparseableStepException()
            );
        }
    }
}
