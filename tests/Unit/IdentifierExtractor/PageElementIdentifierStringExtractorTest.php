<?php

declare(strict_types=1);

namespace webignition\BasilParser\Tests\Unit\IdentifierStringExtractor;

use webignition\BasilParser\IdentifierExtractor\PageElementIdentifierExtractor;
use webignition\BasilParser\Tests\DataProvider\PageElementIdentifierStringDataProviderTrait;

class PageElementIdentifierStringExtractorTest extends \PHPUnit\Framework\TestCase
{
    use PageElementIdentifierStringDataProviderTrait;

    /**
     * @var PageElementIdentifierExtractor
     */
    private $extractor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extractor = new PageElementIdentifierExtractor();
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
        $this->assertTrue($this->extractor->handles('"quoted"'));
    }

    /**
     * @dataProvider unhandledStringsDataProvider
     */
    public function testExtractFromStartReturnsNull(string $string)
    {
        $this->assertNull($this->extractor->extract($string));
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
     * @dataProvider pageElementIdentifierStringDataProvider
     */
    public function testExtractFromStartReturnsString(string $string, string $expectedIdentifierString)
    {
        $identifierString = $this->extractor->extract($string);

        $this->assertSame($expectedIdentifierString, $identifierString);
    }
}
