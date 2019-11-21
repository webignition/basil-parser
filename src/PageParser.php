<?php

declare(strict_types=1);

namespace webignition\BasilParser;

use webignition\BasilDataStructure\Page;

class PageParser
{
    private const KEY_URL = 'url';
    private const KEY_ELEMENTS = 'elements';

    public static function create(): PageParser
    {
        return new PageParser();
    }

    public function parse(array $pageData): Page
    {
        $url = $pageData[self::KEY_URL] ?? '';
        $url = is_string($url) ? trim($url) : '';

        $elements = $pageData[self::KEY_ELEMENTS] ?? [];
        $elements = is_array($elements) ? $elements : [];

        return new Page($url, $elements);
    }
}
