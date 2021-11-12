<?php

declare(strict_types=1);

namespace webignition\BasilParser\Test;

use webignition\BasilModels\Test\Imports;
use webignition\BasilModels\Test\ImportsInterface;
use webignition\PathResolver\PathResolver;

class ImportsParser
{
    private const KEY_STEPS = 'steps';
    private const KEY_DATA_PROVIDERS = 'data_providers';
    private const KEY_PAGES = 'pages';

    private PathResolver $pathResolver;

    public function __construct(PathResolver $pathResolver)
    {
        $this->pathResolver = $pathResolver;
    }

    public static function create(): ImportsParser
    {
        return new ImportsParser(
            new PathResolver()
        );
    }

    /**
     * @param array<string, mixed> $importsData
     */
    public function parse(string $basePath, array $importsData): ImportsInterface
    {
        $imports = new Imports();

        $filteredPathSets = $this->filterPathSets($importsData);
        $resolvedPathSets = $this->resolvePathSets($basePath, $filteredPathSets);

        $imports = $imports->withStepPaths($resolvedPathSets[self::KEY_STEPS]);
        $imports = $imports->withDataProviderPaths($resolvedPathSets[self::KEY_DATA_PROVIDERS]);

        return $imports->withPagePaths($resolvedPathSets[self::KEY_PAGES]);
    }

    /**
     * @param array<string, array<string, string>> $pathSets
     *
     * @return array<string, array<string, string>>
     */
    private function resolvePathSets(string $basePath, array $pathSets): array
    {
        foreach ($pathSets as $key => $pathSet) {
            $pathSets[$key] = $this->resolvePathSet($basePath, $pathSet);
        }

        return $pathSets;
    }

    /**
     * @param array<string, string> $paths
     *
     * @return array<string, string>
     */
    private function resolvePathSet(string $basePath, array $paths): array
    {
        $resolvedPaths = [];

        foreach ($paths as $importName => $path) {
            if (is_string($path)) {
                $resolvedPaths[$importName] = $this->pathResolver->resolve($basePath, $path);
            }
        }

        return $resolvedPaths;
    }

    /**
     * @param array<string, mixed> $importsData
     *
     * @return array<string, array<string, string>>
     */
    private function filterPathSets(array $importsData): array
    {
        $data = [];

        $keys = [
            self::KEY_DATA_PROVIDERS,
            self::KEY_PAGES,
            self::KEY_STEPS,
        ];

        foreach ($keys as $key) {
            $data[$key] = $this->filterPathSet($key, $importsData);
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $importsData
     *
     * @return array<string, string>
     */
    private function filterPathSet(string $key, array $importsData): array
    {
        $importData = $importsData[$key] ?? [];

        return is_array($importData) ? $importData : [];
    }
}
