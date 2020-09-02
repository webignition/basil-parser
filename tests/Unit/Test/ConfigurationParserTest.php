<?php

declare(strict_types=1);

namespace webignition\BasilParser\Tests\Unit\Test;

use PHPUnit\Framework\TestCase;
use webignition\BasilModels\Test\Configuration;
use webignition\BasilModels\Test\ConfigurationInterface;
use webignition\BasilParser\Test\ConfigurationParser;

class ConfigurationParserTest extends TestCase
{
    /**
     * @dataProvider parseDataProvider
     *
     * @param array<mixed> $configurationData
     * @param ConfigurationInterface $expectedConfiguration
     */
    public function testParse(array $configurationData, ConfigurationInterface $expectedConfiguration)
    {
        $parser = ConfigurationParser::create();

        $this->assertEquals($expectedConfiguration, $parser->parse($configurationData));
    }

    public function parseDataProvider(): array
    {
        return [
            'empty' => [
                'configurationData' => [],
                'expectedConfiguration' => new Configuration([], ''),
            ],
            'invalid browser; not an array' => [
                'configurationData' => [
                    'browsers' => true,
                ],
                'expectedConfiguration' => new Configuration([], ''),
            ],
            'valid browser, singular' => [
                'configurationData' => [
                    'browsers' => ['chrome'],
                ],
                'expectedConfiguration' => new Configuration(['chrome'], ''),
            ],
            'valid browser, multiple' => [
                'configurationData' => [
                    'browsers' => ['chrome', 'firefox'],
                ],
                'expectedConfiguration' => new Configuration(['chrome', 'firefox'], ''),
            ],
            'invalid url; not a string' => [
                'configurationData' => [
                    'url' => true,
                ],
                'expectedConfiguration' => new Configuration([''], ''),
            ],
            'valid url' => [
                'configurationData' => [
                    'url' => 'http://example.com/',
                ],
                'expectedConfiguration' => new Configuration([''], 'http://example.com/'),
            ],
            'valid' => [
                'configurationData' => [
                    'browsers' => ['chrome'],
                    'url' => 'http://example.com/',
                ],
                'expectedConfiguration' => new Configuration(['chrome'], 'http://example.com/'),
            ],
        ];
    }
}
