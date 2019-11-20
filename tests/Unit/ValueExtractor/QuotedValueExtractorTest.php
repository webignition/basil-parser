<?php

namespace webignition\BasilParser\Tests\Unit\ValueExtractor;

use webignition\BasilParser\ValueExtractor\QuotedValueExtractor;

class QuotedValueExtractorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var QuotedValueExtractor
     */
    private $quotedValueExtractor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->quotedValueExtractor = new QuotedValueExtractor();
    }

    /**
     * @dataProvider createFromValueStringDataProvider
     */
    public function testExtract(string $valueString, string $expectedValue)
    {
        $value = $this->quotedValueExtractor->extract($valueString);

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
        ];
    }
}
