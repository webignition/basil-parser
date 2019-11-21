<?php

declare(strict_types=1);

namespace webignition\BasilParser\Test;

use webignition\BasilDataStructure\Test\Configuration;

class ConfigurationParser
{
    private const KEY_BROWSER = 'browser';
    private const KEY_URL = 'url';

    public static function create(): ConfigurationParser
    {
        return new ConfigurationParser();
    }

    public function parse(array $configurationData): Configuration
    {
        $browser = $configurationData[self::KEY_BROWSER] ?? '';
        $browser = is_string($browser) ? $browser : '';

        $url = $configurationData[self::KEY_URL] ?? '';
        $url = is_string($url) ? $url : '';

        return new Configuration($browser, $url);
    }
}
