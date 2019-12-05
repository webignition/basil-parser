<?php

namespace webignition\BasilParser\Tests\Unit\ValueExtractor;

use webignition\BasilParser\Tests\DataProvider\QuotedValueDataProviderTrait;
use webignition\BasilParser\ValueExtractor\QuotedValueExtractor;

class QuotedValueExtractorTest extends \PHPUnit\Framework\TestCase
{
    use QuotedValueDataProviderTrait;

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
     * @dataProvider quotedValueDataProvider
     */
    public function testExtract(string $valueString, string $expectedValue)
    {
        $value = $this->quotedValueExtractor->extract($valueString);

        $this->assertSame($expectedValue, $value);
    }
}
