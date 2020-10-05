<?php

namespace PhpSolution\SwaggerUIGen\Component;

use PhpSolution\SwaggerUIGen\Component\DataNormalizer\DataNormalizerInterface;
use PhpSolution\SwaggerUIGen\Component\DataNormalizer\OpenapiNormalizer;
use PhpSolution\SwaggerUIGen\Component\DataProvider\DataProviderInterface;
use PhpSolution\SwaggerUIGen\Component\ModelHandler\SwaggerBuilderInterface;
use PhpSolution\SwaggerUIGen\Component\Model\OpenAPI;

/**
 * Class SwaggerProvider
 *
 * @package PhpSolution\SwaggerUIGen\Component
 */
class SwaggerProvider
{
    /**
     * @var OpenapiNormalizer
     */
    private $openapiNormalizer;
    /**
     * @var \SplObjectStorage|SwaggerBuilderInterface[]
     */
    private $swaggerBuilders;
    /**
     * @var \SplObjectStorage|DataNormalizerInterface[]
     */
    private $dataNormalizers;
    /**
     * @var SwaggerModelExtractor
     */
    private $modelExtractor;

    /**
     * SwaggerProvider constructor.
     *
     * @param null|OpenapiNormalizer $openapiNormalizer
     */
    public function __construct(?OpenapiNormalizer $openapiNormalizer)
    {
        $this->openapiNormalizer = $openapiNormalizer;
        $this->swaggerBuilders = new \SplObjectStorage();
        $this->dataNormalizers = new \SplObjectStorage();
    }

    /**
     * @param DataProviderInterface $configProvider
     *
     * @return array
     */
    public function getSwaggerData(DataProviderInterface $configProvider): array
    {
        $inputConfigs = $configProvider->getData();
        $swaggerModel = $this->createSwaggerModel($inputConfigs);
        if (!$this->modelExtractor instanceof SwaggerModelExtractor) {
            $this->modelExtractor = new SwaggerModelExtractor();
        }
        $config = array_merge_recursive($inputConfigs, $this->modelExtractor->extract($swaggerModel));

        if (!$this->openapiNormalizer) {
            $this->openapiNormalizer = new OpenapiNormalizer();
        }

        return $this->openapiNormalizer->normalize($config);
    }

    /**
     * @param array $inputConfigs
     *
     * @return OpenAPI
     */
    public function createSwaggerModel(array $inputConfigs): OpenAPI
    {
        $normalizedConfigs = [];
        foreach ($this->dataNormalizers as $normalizer) {
            $normalizedConfigs = array_merge($normalizedConfigs, $normalizer->normalize($inputConfigs));
        }
        $swagger = new OpenAPI();
        foreach ($this->swaggerBuilders as $swaggerBuilder) {
            $swaggerBuilder->build($swagger, $inputConfigs);
        }

        return $swagger;
    }

    /**
     * @param DataNormalizerInterface $normalizer
     */
    public function addDataNormalizer(DataNormalizerInterface $normalizer): void
    {
        $this->dataNormalizers->attach($normalizer);
    }

    /**
     * @param SwaggerBuilderInterface $builder
     */
    public function addSwaggerBuilder(SwaggerBuilderInterface $builder): void
    {
        $this->swaggerBuilders->attach($builder);
    }
}