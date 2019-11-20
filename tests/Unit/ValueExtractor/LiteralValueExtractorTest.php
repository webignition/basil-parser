<?php

declare(strict_types=1);

namespace webignition\BasilParser\Tests\Unit\ValueExtractor;

use webignition\BasilParser\ValueExtractor\LiteralValueExtractor;
use webignition\BasilParser\Tests\DataProvider\LiteralParameterStringDataProviderTrait;

class LiteralValueExtractorTest extends \PHPUnit\Framework\TestCase
{
    use LiteralParameterStringDataProviderTrait;

    /**
     * @var \webignition\BasilParser\ValueExtractor\LiteralValueExtractor
     */
    private $extractor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extractor = new LiteralValueExtractor();
    }

    /**
     * @dataProvider unhandledStringsDataProvider
     */
    public function testHandlesReturnsFalse(string $string)
    {
        $this->assertFalse($this->extractor->handles($string));
    }

    public function testHandlesReturnsTrue()
    {
        $this->assertTrue($this->extractor->handles('reference'));
    }

    /**
     * @dataProvider unhandledStringsDataProvider
     */
    public function testExtractReturnsEmptyValue(string $string)
    {
        $this->assertSame('', $this->extractor->extract($string));
    }

    public function unhandledStringsDataProvider(): array
    {
        return [
            'empty' => [
                'string' => '',
            ],
            'quoted value' => [
                'string' => '"not handled"',
            ],
            'variable value' => [
                'string' => '$elements.element_name',
            ],
        ];
    }

    /**
     * @dataProvider literalParameterStringDataProvider
     */
    public function testExtractFromStartReturnsString(string $string, string $expectedIdentifierString)
    {
        $identifierString = $this->extractor->extract($string);

        $this->assertSame($expectedIdentifierString, $identifierString);
    }
}
