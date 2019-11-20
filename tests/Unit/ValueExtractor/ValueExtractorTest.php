<?php

namespace webignition\BasilParser\Tests\Unit\ValueExtractor;

use webignition\BasilParser\ValueExtractor\ValueExtractor;

class ValueExtractorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ValueExtractor
     */
    private $valueExtractor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->valueExtractor = ValueExtractor::create();
    }

    /**
     * @dataProvider createFromValueStringDataProvider
     */
    public function testExtract(string $valueString, string $expectedValue)
    {
        $value = $this->valueExtractor->extract($valueString);

        $this->assertSame($expectedValue, $value);
    }

    public function createFromValueStringDataProvider(): array
    {
        return [
            'empty' => [
                'valueString' => '',
                'expectedValue' => '',
            ],
            'quoted string' => [
                'valueString' => '"value"',
                'expectedValue' => '"value"',
            ],
            'quoted string lacking final quote' => [
                'valueString' => '"value',
                'expectedValue' => '"value"',
            ],
            'quoted string with trailing data' => [
                'valueString' => '"value" trailing',
                'expectedValue' => '"value"',
            ],
            'quoted string with escaped quotes' => [
                'valueString' => '"\"value\""',
                'expectedValue' => '"\"value\""',
            ],
            'quoted string with escaped quotes with trailing data' => [
                'valueString' => '"\"value\"" trailing',
                'expectedValue' => '"\"value\""',
            ],
            'variable parameter' => [
                'valueString' => '$data.data_name',
                'expectedValue' => '$data.data_name',
            ],
            'variable parameter with trailing data' => [
                'valueString' => '$data.data_name trailing',
                'expectedValue' => '$data.data_name',
            ],
            'variable parameter with default' => [
                'valueString' => '$data.data_name|"default"',
                'expectedValue' => '$data.data_name|"default"',
            ],
            'variable parameter with default with trailing data' => [
                'valueString' => '$data.data_name|"default" trailing',
                'expectedValue' => '$data.data_name|"default"',
            ],
        ];
    }
}
