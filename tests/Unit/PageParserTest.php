<?php

declare(strict_types=1);

namespace webignition\BasilParser\Tests\Unit;

use PHPUnit\Framework\TestCase;
use webignition\BasilDataStructure\Page;
use webignition\BasilParser\PageParser;

class PageParserTest extends TestCase
{
    /**
     * @dataProvider parseDataProvider
     */
    public function testParse(array $pageData, Page $expectedPage)
    {
        $parser = PageParser::create();

        $this->assertEquals($expectedPage, $parser->parse($pageData));
    }

    public function parseDataProvider(): array
    {
        return [
            'empty' => [
                'pageData' => [],
                'expectedPage' => new Page(''),
            ],
            'invalid url; not a string' => [
                'pageData' => [
                    'url' => true,
                ],
                'expectedPage' => new Page(''),
            ],
            'valid url' => [
                'pageData' => [
                    'url' => 'http://example.com/',
                ],
                'expectedPage' => new Page('http://example.com/'),
            ],
            'invalid elements; not an array' => [
                'pageData' => [
                    'elements' => 'string',
                ],
                'expectedPage' => new Page('', []),
            ],
            'valid elements' => [
                'pageData' => [
                    'elements' => [
                        'heading' => 'page_import_name.elements.heading',
                    ],
                ],
                'expectedPage' => new Page('', [
                    'heading' => 'page_import_name.elements.heading',
                ]),
            ],
        ];
    }
}
