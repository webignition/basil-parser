<?php

declare(strict_types=1);

namespace webignition\BasilParser\Tests\Unit\Test;

use PHPUnit\Framework\TestCase;
use webignition\BasilDataStructure\Test\Configuration;
use webignition\BasilParser\Test\ConfigurationParser;

class ConfigurationParserTest extends TestCase
{
    /**
     * @dataProvider parseDataProvider
     */
    public function testParse(array $configurationData, Configuration $expectedConfiguration)
    {
        $parser = ConfigurationParser::create();

        $this->assertEquals($expectedConfiguration, $parser->parse($configurationData));
    }

    public function parseDataProvider(): array
    {
        return [
            'empty' => [
                'configurationData' => [],
                'expectedConfiguration' => new Configuration('', ''),
            ],
            'invalid browser; not a string' => [
                'configurationData' => [
                    'browser' => true,
                ],
                'expectedConfiguration' => new Configuration('', ''),
            ],
            'valid browser' => [
                'configurationData' => [
                    'browser' => 'chrome',
                ],
                'expectedConfiguration' => new Configuration('chrome', ''),
            ],
            'invalid url; not a string' => [
                'configurationData' => [
                    'url' => true,
                ],
                'expectedConfiguration' => new Configuration('', ''),
            ],
            'valid url' => [
                'configurationData' => [
                    'url' => 'http://example.com/',
                ],
                'expectedConfiguration' => new Configuration('', 'http://example.com/'),
            ],
            'valid' => [
                'configurationData' => [
                    'browser' => 'chrome',
                    'url' => 'http://example.com/',
                ],
                'expectedConfiguration' => new Configuration('chrome', 'http://example.com/'),
            ],
        ];
    }
}
