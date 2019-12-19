<?php

declare(strict_types=1);

namespace webignition\BasilParser\Tests\Unit\ValueExtractor;

use webignition\BasilParser\Tests\DataProvider\DescendantPageElementIdentifierStringDataProviderTrait;
use webignition\BasilParser\Tests\DataProvider\PageElementIdentifierStringDataProviderTrait;
use webignition\BasilParser\Tests\DataProvider\QuotedValueDataProviderTrait;
use webignition\BasilParser\Tests\DataProvider\VariableParameterIdentifierStringDataProviderTrait;
use webignition\BasilParser\ValueExtractor\ValueExtractor;

class ValueExtractorTest extends \PHPUnit\Framework\TestCase
{
    use PageElementIdentifierStringDataProviderTrait;
    use QuotedValueDataProviderTrait;
    use VariableParameterIdentifierStringDataProviderTrait;
    use DescendantPageElementIdentifierStringDataProviderTrait;

    /**
     * @var ValueExtractor
     */
    private $extractor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extractor = ValueExtractor::create();
    }

    /**
     * @dataProvider unhandledStringsDataProvider
     */
    public function testExtractReturnsEmptyValue(string $string)
    {
        $this->assertNull($this->extractor->extract($string));
    }

    public function unhandledStringsDataProvider(): array
    {
        return [
            'empty' => [
                'string' => '',
            ],
            'unquoted literal' => [
                'string' => 'value',
            ],
        ];
    }

    /**
     * @dataProvider pageElementIdentifierStringDataProvider
     * @dataProvider quotedValueDataProvider
     * @dataProvider variableParameterIdentifierStringDataProvider
     * @dataProvider descendantPageElementIdentifierStringDataProvider
     */
    public function testExtractReturnsString(string $string, string $expectedIdentifierString)
    {
        $identifierString = $this->extractor->extract($string);

        $this->assertSame($expectedIdentifierString, $identifierString);
    }
}
