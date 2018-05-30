<?php
declare(strict_types=1);

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation\AnnotationBuilder;

use PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Schema\PhpDoc\SimpleAnnotationParser;
use PhpSolution\SwaggerUIGen\Component\Model\Operation;
use Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface;

/**
 * AbstractParametersBuilder
 */
abstract class AbstractParametersBuilder
{
    /**
     * @var MetadataFactoryInterface
     */
    protected $classMetadataFactory;

    /**
     * @var SimpleAnnotationParser
     */
    protected $annotationParser;

    /**
     * @var string
     */
    protected $annotationName;

    /**
     * @param MetadataFactoryInterface $classMetadataFactory
     * @param SimpleAnnotationParser   $annotationParser
     * @param string                   $annotationName
     */
    public function __construct(MetadataFactoryInterface $classMetadataFactory, SimpleAnnotationParser $annotationParser, string $annotationName)
    {
        $this->classMetadataFactory = $classMetadataFactory;
        $this->annotationName = $annotationName;
        $this->annotationParser = $annotationParser;
    }

    /**
     * @param Operation $operation
     * @param string    $className
     */
    abstract public function build(Operation $operation, string $className): void;

    /**
     * @param string $className
     *
     * @return \ReflectionClass
     */
    protected function getReflectionClass(string $className): \ReflectionClass
    {
        return new \ReflectionClass($className);
    }

    /**
     * @param string $property
     *
     * @return string
     */
    protected function normalizePropertyName(string $property): string
    {
        return strtolower(preg_replace('/[A-Z]/', '_\\0', $property));
    }
}
