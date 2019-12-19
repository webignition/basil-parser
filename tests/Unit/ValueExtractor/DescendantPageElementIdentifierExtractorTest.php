<?php

declare(strict_types=1);

namespace webignition\BasilParser\Tests\Unit\ValueExtractor;

use webignition\BasilParser\Tests\DataProvider\DescendantPageElementIdentifierStringDataProviderTrait;
use webignition\BasilParser\ValueExtractor\DescendantPageElementIdentifierExtractor;

class DescendantPageElementIdentifierExtractorTest extends \PHPUnit\Framework\TestCase
{
    use DescendantPageElementIdentifierStringDataProviderTrait;

    /**
     * @var DescendantPageElementIdentifierExtractor
     */
    private $extractor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extractor = DescendantPageElementIdentifierExtractor::createExtractor();
    }

    /**
     * @dataProvider unhandledStringsDataProvider
     */
    public function testHandlesReturnsFalse(string $string)
    {
        $this->assertFalse($this->extractor->handles($string));
    }

    public function unhandledStringsDataProvider(): array
    {
        return [
            'empty' => [
                'string' => '',
            ],
            'variable value' => [
                'string' => '$elements.element_name',
            ],
        ];
    }

    /**
     * @dataProvider handlesDataProvider
     */
    public function testHandles(string $string, bool $expectedHandles)
    {
        $this->assertSame($expectedHandles, $this->extractor->handles($string));
    }

    public function handlesDataProvider(): array
    {
        return [
            'valid descendant identifier' => [
                'string' => '{{ $".parent" }} $".child"',
                'expectedHandles' => true,
            ],
        ];
    }


    /**
     * @dataProvider unhandledStringsDataProvider
     * @dataProvider returnsEmptyValueDataProvider
     */
    public function testExtractReturnsEmptyValue(string $string)
    {
        $this->assertSame('', $this->extractor->extract($string));
    }

    public function returnsEmptyValueDataProvider(): array
    {
        return [
            'invalid parent identifier' => [
                'string' => '{{ .parent }} $".child"',
            ],
            'invalid child identifier' => [
                'string' => '{{ $".parent" }} .child',
            ],
        ];
    }

    /**
     * @dataProvider descendantPageElementIdentifierStringDataProvider
     */
    public function testExtractReturnsString(string $string, string $expectedIdentifierString)
    {
        $identifierString = $this->extractor->extract($string);

        $this->assertSame($expectedIdentifierString, $identifierString);
    }
}
