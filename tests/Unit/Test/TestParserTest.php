<?php

declare(strict_types=1);

namespace webignition\BasilParser\Tests\Unit\Test;

use PHPUnit\Framework\TestCase;
use webignition\BasilDataStructure\Action\InteractionAction;
use webignition\BasilDataStructure\Assertion;
use webignition\BasilDataStructure\DataSetCollection;
use webignition\BasilDataStructure\Step;
use webignition\BasilDataStructure\Test\Configuration;
use webignition\BasilDataStructure\Test\Imports;
use webignition\BasilDataStructure\Test\Test;
use webignition\BasilParser\Test\TestParser;

class TestParserTest extends TestCase
{
    /**
     * @dataProvider parseDataProvider
     */
    public function testParse(string $basePath, string $name, array $testData, Test $expectedTest)
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
                'expectedTest' => (new Test(
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
                                new Assertion(
                                    '$page.title is $data.expected_title',
                                    '$page.title',
                                    'is',
                                    '$data.expected_title'
                                )
                            ]
                        ))->withDataImportName('data_provider_import_name'),
                    ]
                ))->withImports((new Imports())
                    ->withStepPaths([
                        'step_import_name' => '/basil/step/one.yml',
                    ])
                    ->withDataProviderPaths([
                        'data_provider_import_name' => '/basil/data/data.yml',
                    ])
                    ->withPagePaths([
                        'page_import_name' => '/basil/page/page.yml',
                    ])),
            ],
        ];
    }
}
