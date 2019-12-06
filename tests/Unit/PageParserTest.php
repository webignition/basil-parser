<?php

declare(strict_types=1);

namespace webignition\BasilParser\Tests\Unit;

use PHPUnit\Framework\TestCase;
use webignition\BasilModels\Page\Page;
use webignition\BasilModels\Page\PageInterface;
use webignition\BasilParser\PageParser;

class PageParserTest extends TestCase
{
    /**
     * @dataProvider parseDataProvider
     */
    public function testParse(string $importName, array $pageData, PageInterface $expectedPage)
    {
        $parser = PageParser::create();

        $this->assertEquals($expectedPage, $parser->parse($importName, $pageData));
    }

    public function parseDataProvider(): array
    {
        return [
            'empty' => [
                'importName' => '',
                'pageData' => [],
                'expectedPage' => new Page('', ''),
            ],
            'invalid url; not a string' => [
                'importName' => 'import_name',
                'pageData' => [
                    'url' => true,
                ],
                'expectedPage' => new Page('import_name', ''),
            ],
            'valid url' => [
                'importName' => 'import_name',
                'pageData' => [
                    'url' => 'http://example.com/',
                ],
                'expectedPage' => new Page('import_name', 'http://example.com/'),
            ],
            'invalid elements; not an array' => [
                'importName' => '',
                'pageData' => [
                    'elements' => 'string',
                ],
                'expectedPage' => new Page('', '', []),
            ],
            'valid elements' => [
                'importName' => '',
                'pageData' => [
                    'elements' => [
                        'heading' => 'page_import_name.elements.heading',
                    ],
                ],
                'expectedPage' => new Page('', '', [
                    'heading' => 'page_import_name.elements.heading',
                ]),
            ],
        ];
    }
}
