<?php

declare(strict_types=1);

namespace webignition\BasilParser\Tests\Unit\Test;

use PHPUnit\Framework\TestCase;
use webignition\BasilModels\Action\InteractionAction;
use webignition\BasilModels\Assertion\ComparisonAssertion;
use webignition\BasilModels\DataSet\DataSetCollection;
use webignition\BasilModels\Step\Step;
use webignition\BasilModels\Test\Configuration;
use webignition\BasilModels\Test\Imports;
use webignition\BasilModels\Test\Test;
use webignition\BasilModels\Test\TestInterface;
use webignition\BasilParser\Test\TestParser;

class TestParserTest extends TestCase
{
    /**
     * @dataProvider parseDataProvider
     */
    public function testParse(string $basePath, string $name, array $testData, TestInterface $expectedTest)
    {
        $parser = TestParser::create();

        $this->assertEquals($expectedTest, $parser->parse($basePath, $name, $testData));
    }

    public function parseDataProvider(): array
    {
        return [
            'empty' => [
                'basePath' => '/basil/',
                'name' => 'empty-test.yml',
                'testData' => [],
                'expectedTest' => new Test(
                    'empty-test.yml',
                    new Configuration('', ''),
                    []
                ),
            ],
            'non-empty' => [
                'basePath' => '/basil/',
                'name' => 'non-empty-test.yml',
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
                    'non-empty-test.yml',
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
                    ],
                    (new Imports())
                        ->withStepPaths([
                            'step_import_name' => '/basil/step/one.yml',
                        ])
                        ->withDataProviderPaths([
                            'data_provider_import_name' => '/basil/data/data.yml',
                        ])
                        ->withPagePaths([
                            'page_import_name' => '/basil/page/page.yml',
                        ])
                ),
            ],
        ];
    }
}
