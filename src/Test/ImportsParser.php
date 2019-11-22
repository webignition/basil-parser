<?php

declare(strict_types=1);

namespace webignition\BasilParser\Test;

use webignition\BasilDataStructure\Test\Imports;
use webignition\PathResolver\PathResolver;

class ImportsParser
{
    private const KEY_STEPS = 'steps';
    private const KEY_DATA_PROVIDERS = 'data_providers';
    private const KEY_PAGES = 'pages';

    private $pathResolver;

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

    public function parse(string $basePath, array $importsData): Imports
    {
        $imports = new Imports();

        $filteredPathSets = $this->filterPathSets($importsData);
        $resolvedPathSets = $this->resolvePathSets($basePath, $filteredPathSets);

        $imports = $imports->withStepPaths($resolvedPathSets[self::KEY_STEPS]);
        $imports = $imports->withDataProviderPaths($resolvedPathSets[self::KEY_DATA_PROVIDERS]);
        $imports = $imports->withPagePaths($resolvedPathSets[self::KEY_PAGES]);

        return $imports;
    }

    private function resolvePathSets(string $basePath, array $pathSets): array
    {
        foreach ($pathSets as $key => $pathSet) {
            $pathSets[$key] = $this->resolvePathSet($basePath, $pathSet);
        }

        return $pathSets;
    }

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

    private function filterPathSet(string $key, array $importsData): array
    {
        $importData = $importsData[$key] ?? [];
        $importData = is_array($importData) ? $importData : [];

        return $importData;
    }
}
