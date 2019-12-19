<?php

declare(strict_types=1);

namespace webignition\BasilParser\Tests\Unit\ValueExtractor;

use webignition\BasilParser\ValueExtractor\PageElementIdentifierExtractor;
use webignition\BasilParser\Tests\DataProvider\PageElementIdentifierStringDataProviderTrait;

class PageElementIdentifierExtractorTest extends \PHPUnit\Framework\TestCase
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
            'variable value' => [
                'string' => '$elements.element_name',
            ],
        ];
    }

    /**
     * @dataProvider pageElementIdentifierStringDataProvider
     */
    public function testExtractReturnsString(string $string, string $expectedIdentifierString)
    {
        $identifierString = $this->extractor->extract($string);

        $this->assertSame($expectedIdentifierString, $identifierString);
    }
}
