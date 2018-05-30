<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation;

use PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation\AnnotationBuilder\BodyParametersBuilder;
use PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation\AnnotationBuilder\FormParametersBuilder;
use PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Schema\PhpDoc\SimpleAnnotationParser;
use PhpSolution\SwaggerUIGen\Component\Model\Operation;
use PhpSolution\SwaggerUIGen\Component\Model\Parameter;
use Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface;

/**
 * Class AnnotationBuilder
 */
class AnnotationBuilder implements OperationBuilderInterface
{
    /**
     * @var FormParametersBuilder
     */
    private $formParametersBuilder;

    /**
     * @var BodyParametersBuilder
     */
    private $bodyParametersBuilder;

    /**
     * @param MetadataFactoryInterface $classMetadataFactory
     * @param string                   $annotationName
     */
    public function __construct(MetadataFactoryInterface $classMetadataFactory, string $annotationName = 'api')
    {
        $annotationParser = new SimpleAnnotationParser();
        $this->formParametersBuilder = new FormParametersBuilder($classMetadataFactory, $annotationParser, $annotationName);
        $this->bodyParametersBuilder = new BodyParametersBuilder($classMetadataFactory, $annotationParser, $annotationName);
    }

    /**
     * @param Operation $operation
     * @param array     $generalConfig
     */
    public function build(Operation $operation, array $generalConfig): void
    {
        if (!isset($generalConfig['request']['class'])) {
            return;
        }
        $className = $generalConfig['request']['class'];
        $type = $generalConfig['request']['in'] ?? Parameter::IN_BODY;

        $type == Parameter::IN_BODY
            ? $this->bodyParametersBuilder->build($operation, $className)
            : $this->formParametersBuilder->build($operation, $className);
    }
}
