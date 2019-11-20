<?php

declare(strict_types=1);

namespace webignition\BasilParser\Tests\Unit\IdentifierExtractor;

use webignition\BasilParser\IdentifierExtractor\VariableParameterIdentifierExtractor;
use webignition\BasilParser\Tests\DataProvider\VariableParameterIdentifierStringDataProviderTrait;

class VariableParameterIdentifierStringExtractorTest extends \PHPUnit\Framework\TestCase
{
    use VariableParameterIdentifierStringDataProviderTrait;

    /**
     * @var VariableParameterIdentifierExtractor
     */
    private $extractor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extractor = new VariableParameterIdentifierExtractor();
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
