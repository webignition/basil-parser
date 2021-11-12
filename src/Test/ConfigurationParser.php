<?php

declare(strict_types=1);

namespace webignition\BasilParser\Test;

use webignition\BasilModels\Test\Configuration;
use webignition\BasilModels\Test\ConfigurationInterface;

class ConfigurationParser
{
    private const KEY_BROWSER = 'browser';
    private const KEY_URL = 'url';

    public static function create(): ConfigurationParser
    {
        return new ConfigurationParser();
    }

    /**
     * @param array<string, mixed> $configurationData
     */
    public function parse(array $configurationData): ConfigurationInterface
    {
        $browser = $configurationData[self::KEY_BROWSER] ?? '';
        $browser = is_string($browser) ? $browser : '';

        $url = $configurationData[self::KEY_URL] ?? '';
        $url = is_string($url) ? $url : '';

        return new Configuration($browser, $url);
    }
}
