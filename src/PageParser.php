<?php

declare(strict_types=1);

namespace webignition\BasilParser;

use webignition\BasilModels\Page\Page;
use webignition\BasilModels\Page\PageInterface;

class PageParser
{
    private const KEY_URL = 'url';
    private const KEY_ELEMENTS = 'elements';

    public static function create(): PageParser
    {
        return new PageParser();
    }

    /**
     * @param string $importName
     * @param array<string, mixed> $pageData
     *
     * @return PageInterface
     */
    public function parse(string $importName, array $pageData): PageInterface
    {
        $url = $pageData[self::KEY_URL] ?? '';
        $url = is_string($url) ? trim($url) : '';

        $elements = $pageData[self::KEY_ELEMENTS] ?? [];
        $elements = is_array($elements) ? $elements : [];

        return new Page($importName, $url, $elements);
    }
}
