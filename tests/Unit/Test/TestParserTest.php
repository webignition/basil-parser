<?php

declare(strict_types=1);

namespace webignition\BasilParser\Tests\Unit\Test;

use PHPUnit\Framework\TestCase;
use webignition\BasilModels\Action\InteractionAction;
use webignition\BasilModels\Assertion\ComparisonAssertion;
use webignition\BasilModels\DataSet\DataSetCollection;
use webignition\BasilModels\Step\Step;
use webignition\BasilModels\Test\Configuration;
use webignition\BasilModels\Test\Test;
use webignition\BasilModels\Test\TestInterface;
use webignition\BasilParser\Exception\UnparseableActionException;
use webignition\BasilParser\Exception\UnparseableStepException;
use webignition\BasilParser\Exception\UnparseableTestException;
use webignition\BasilParser\Test\TestParser;

class TestParserTest extends TestCase
{
    /**
     * @var TestParser
     */
    private $parser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = TestParser::create();
    }

    /**
     * @dataProvider parseDataProvider
     *
     * @param array<mixed> $testData
     * @param TestInterface $expectedTest
     */
    public function testParse(array $testData, TestInterface $expectedTest)
    {
        $this->assertEquals($expectedTest, $this->parser->parse($testData));
    }

    public function parseDataProvider(): array
    {
        return [
            'empty' => [
                'testData' => [],
                'expectedTest' => new Test(
                    new Configuration('', ''),
                    []
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
                    new Configuration('chrome', 'http://example.com/'),
                    [
                        'step one' => (new Step([], []))
                            ->withImportName('step_import_name')
                            ->withData(new DataSetCollection([
                                'set1' => [
                                    'key1' => 'value1',
                                ],
                            ])),
                        'step two' => (new Step(
                            [
                                new InteractionAction(
                                    'click $page_import_name.elements.button',
                                    'click',
                                    '$page_import_name.elements.button',
                                    '$page_import_name.elements.button'
                                )
                            ],
                            [
                                new ComparisonAssertion(
                                    '$page.title is $data.expected_title',
                                    '$page.title',
                                    'is',
                                    '$data.expected_title'
                                )
                            ]
                        ))->withDataImportName('data_provider_import_name'),
                    ]
                ),
            ],
        ];
    }

    public function testParseTestWithStepWithEmptyAction()
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

            $expectedUnparseableStepException = UnparseableStepException::createForUnparseableActionException(
                [
                    'actions' => [
                        '',
                    ],
                ],
                UnparseableActionException::createEmptyActionException()
            );

            $this->assertEquals(
                $expectedUnparseableStepException,
                $unparseableTestException->getUnparseableStepException()
            );
        }
    }
}
