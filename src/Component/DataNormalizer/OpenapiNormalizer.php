<?php

namespace PhpSolution\SwaggerUIGen\Component\DataNormalizer;

use PhpSolution\SwaggerUIGen\Component\SchemaValidator\SwaggerValidator;

/**
 * Class OpenapiNormalizer
 *
 * @package PhpSolution\SwaggerUIGen\Component\DataNormalizer
 */
class OpenapiNormalizer implements DataNormalizerInterface
{
    private const PROPERTIES = [
        'swagger',
        'host',
        'basePath',
        'schemes',
        'consumes',
        'produces',
        'info',
        'paths',
        'definitions',
        'parameters',
        'responses',
        'securityDefinitions',
        'security',
        'tags',
        'externalDocs',
    ];

    /**
     * @var SwaggerValidator
     */
    private $configValidator;

    /**
     * OpenapiNormalizer constructor.
     *
     * @param SwaggerValidator $validator
     */
    public function __construct(SwaggerValidator $validator)
    {
        $this->configValidator = $validator;
    }

    /**
     * @param array $config
     *
     * @return array
     *
     * @throws NormalizationException
     */
    public function normalize(array $config): array
    {
        $normalizeConfig = array_filter(
            $config,
            function ($value, $key) {
                return !is_null($value) && in_array($key, self::PROPERTIES);
            },
            ARRAY_FILTER_USE_BOTH
        );

        if (count($validationErrors = $this->configValidator->validate($normalizeConfig)) > 0) {
            throw new NormalizationException(
                sprintf("JSON Schema does not validate. Violations:\n%s", implode("\n", $validationErrors))
            );
        } else {
            return $normalizeConfig;
        }
    }
}