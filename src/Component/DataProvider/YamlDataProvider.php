<?php

namespace PhpSolution\SwaggerUIGen\Component\DataProvider;

use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlDataProvider
 *
 * @package PhpSolution\SwaggerUIGen\Component\DataProvider
 */
class YamlDataProvider implements DataProviderInterface
{
    /**
     * @var array
     */
    private $paths;
    /**
     * @var array
     */
    private $defaultsPaths;

    /**
     * YamlDataProvider constructor.
     *
     * @param array $paths
     * @param array $defaultsPaths
     */
    public function __construct(array $paths, array $defaultsPaths)
    {
        $this->paths = $paths;
        $this->defaultsPaths = $defaultsPaths;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $defaultsContent = '';
        foreach ($this->defaultsPaths as $defaultsPath) {
            $defaultsContent .= \file_get_contents($defaultsPath) . PHP_EOL;
        }

        $result = [];
        foreach ($this->paths as $path) {
            $configContent = $defaultsContent . \file_get_contents($path) . PHP_EOL;
            $config = Yaml::parse($configContent);
            $result = \array_merge_recursive($result, $config);
        }

        // Remove defaults
        $defaultsKeys = \array_keys(Yaml::parse($defaultsContent));
        $result = \array_filter(
            $result,
            static function ($key) use ($defaultsKeys) {
                return !\in_array($key, $defaultsKeys);
            },
            ARRAY_FILTER_USE_KEY
        );

        return $result;
    }
}