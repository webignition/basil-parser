<?php

declare(strict_types=1);

namespace webignition\BasilParser\Tests\Unit\ValueExtractor;

use webignition\BasilParser\ValueExtractor\VariableValueExtractor;
use webignition\BasilParser\Tests\DataProvider\VariableParameterIdentifierStringDataProviderTrait;

class VariableValueExtractorTest extends \PHPUnit\Framework\TestCase
{
    use VariableParameterIdentifierStringDataProviderTrait;

    /**
     * @var \webignition\BasilParser\ValueExtractor\VariableValueExtractor
     */
    private $extractor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extractor = new VariableValueExtractor();
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
        $this->assertTrue($this->extractor->handles('$elements.name'));
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
                'string' => '"quoted"',
            ],
        ];
    }

    /**
     * @dataProvider variableParameterIdentifierStringDataProvider
     */
    public function testExtractFromStartReturnsString(string $string, string $expectedIdentifierString)
    {
        $identifierString = $this->extractor->extract($string);

        $this->assertSame($expectedIdentifierString, $identifierString);
    }
}
