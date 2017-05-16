<?php

namespace PhpSolution\SwaggerUIGen\Bundle\Service;

use PhpSolution\SwaggerUIGen\Component\DataProvider\YamlDataProvider;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * Class DataProviderFactory
 *
 * @package PhpSolution\SwaggerUIGen\Bundle\Service
 */
class DataProviderFactory
{
    /**
     * @var FileLocator
     */
    private $fileLocator;

    /**
     * OptionsProviderFactory constructor.
     *
     * @param FileLocator $fileLocator
     */
    public function __construct(FileLocator $fileLocator)
    {
        $this->fileLocator = $fileLocator;
    }

    /**
     * @param array $filePaths
     * @param array $dirPaths
     * @param array $defaultsPaths
     *
     * @return YamlDataProvider
     */
    public function createDataProvider(array $filePaths, array $dirPaths, array $defaultsPaths): YamlDataProvider
    {
        $realPaths = $this->getRealFilePathList($filePaths);
        foreach ($dirPaths as $dirPath) {
            $dir = $this->fileLocator->locate($dirPath);
            /* @var $file \Symfony\Component\Finder\SplFileInfo */
            foreach (Finder::create()->files()->in($dir)->name('*.yml') as $file) {
                $realPaths[] = $file->getPathname();
            }
        }
        $realDefaultsPaths = $this->getRealFilePathList($defaultsPaths);

        return new YamlDataProvider($realPaths, $realDefaultsPaths);
    }

    /**
     * @param array $filePaths
     *
     * @return array
     */
    private function getRealFilePathList(array $filePaths): array
    {
        return array_map(
            function (string $relativeFilePath) {
                return $this->fileLocator->locate($relativeFilePath);
            },
            $filePaths
        );
    }
}