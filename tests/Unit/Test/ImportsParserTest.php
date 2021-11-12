<?php

declare(strict_types=1);

namespace webignition\BasilParser\Tests\Unit\Test;

use PHPUnit\Framework\TestCase;
use webignition\BasilModels\Test\Imports;
use webignition\BasilModels\Test\ImportsInterface;
use webignition\BasilParser\Test\ImportsParser;

class ImportsParserTest extends TestCase
{
    /**
     * @dataProvider parseDataProvider
     *
     * @param string $basePath
     * @param array<mixed> $importsData
     * @param ImportsInterface $expectedImports
     */
    public function testParse(string $basePath, array $importsData, ImportsInterface $expectedImports): void
    {
        $parser = ImportsParser::create();

        $this->assertEquals($expectedImports, $parser->parse($basePath, $importsData));
    }

    /**
     * @return array<mixed>
     */
    public function parseDataProvider(): array
    {
        return [
            'empty' => [
                'basePath' => '',
                'importsData' => [],
                'expectedImports' => new Imports(),
            ],
            'non-empty' => [
                'basePath' => '/base-path/',
                'importsData' => [
                    'steps' => [
                        'name_of_valid_step_path_1' => '/absolute/step.yml',
                        'name_of_valid_step_path_2' => 'relative1/step.yml',
                        'name_of_valid_step_path_3' => './relative1/step.yml',
                        'name_of_valid_step_path_4' => '../relative1/step.yml',
                        'invalid_step_path' => [],
                    ],
                    'data_providers' => [
                        'name_of_valid_data_provider_path' => '/absolute/data.yml',
                    ],
                    'pages' => [
                        'name_of_valid_page_path' => '/absolute/page.yml',
                    ],
                ],
                'expectedImports' => (new Imports())
                    ->withStepPaths([
                        'name_of_valid_step_path_1' => '/absolute/step.yml',
                        'name_of_valid_step_path_2' => '/base-path/relative1/step.yml',
                        'name_of_valid_step_path_3' => '/base-path/relative1/step.yml',
                        'name_of_valid_step_path_4' => '/relative1/step.yml',
                    ])
                    ->withDataProviderPaths([
                        'name_of_valid_data_provider_path' => '/absolute/data.yml',
                    ])
                    ->withPagePaths([
                        'name_of_valid_page_path' => '/absolute/page.yml',
                    ]),
            ],
        ];
    }
}
