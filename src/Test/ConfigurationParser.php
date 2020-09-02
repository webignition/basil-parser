<?php

declare(strict_types=1);

namespace webignition\BasilParser\Test;

use webignition\BasilModels\Test\Configuration;
use webignition\BasilModels\Test\ConfigurationInterface;

class ConfigurationParser
{
    private const KEY_BROWSERS = 'browsers';
    private const KEY_URL = 'url';

    public static function create(): ConfigurationParser
    {
        return new ConfigurationParser();
    }

    /**
     * @param array<string, mixed> $configurationData
     *
     * @return ConfigurationInterface
     */
    public function parse(array $configurationData): ConfigurationInterface
    {
        $browsers = $configurationData[self::KEY_BROWSERS] ?? [];
        $browsers = is_array($browsers) ? $browsers : [];

        $url = $configurationData[self::KEY_URL] ?? '';
        $url = is_string($url) ? $url : '';

        return new Configuration($browsers, $url);
    }
}
