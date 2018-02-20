<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation;

use Doctrine\Bundle\DoctrineBundle\Registry;
use PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Schema\PhpDoc\SimpleAnnotationParser;
use PhpSolution\SwaggerUIGen\Component\Model\Items;
use PhpSolution\SwaggerUIGen\Component\Model\Operation;
use PhpSolution\SwaggerUIGen\Component\Model\Parameter;
use PhpSolution\SwaggerUIGen\Component\Model\ParameterGeneralInfo;
use PhpSolution\SwaggerUIGen\Component\Model\Schema;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;

/**
 * Class FormTypeBuilder
 */
class ClassBuilder implements OperationBuilderInterface
{
    private const CONSUMES_JSON = 'application/json';

    /**
     * @var string
     */
    private $annotationName;

    /**
     * @var SimpleAnnotationParser
     */
    private $annotationParser;

    /**
     * PhpDocParser constructor.
     *
     * @param string $annotationName
     */
    public function __construct(string $annotationName = 'api')
    {
        $this->annotationName = $annotationName;
    }

    /**
     * @return SimpleAnnotationParser
     */
    private function getAnnotationParser(): SimpleAnnotationParser
    {
        return $this->annotationParser ?: $this->annotationParser = new SimpleAnnotationParser();
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
        $refClass = new \ReflectionClass($className);
        $type = $generalConfig['request']['in'] ?? Parameter::IN_BODY;

        if ($type == Parameter::IN_BODY) {
            $operation->setConsumes([self::CONSUMES_JSON]);
            $parameter = new Parameter(Parameter::IN_BODY);
            $parameter->setName(Parameter::IN_BODY);
            $parameter->setRequired(true);

            $schema = new Schema('object');
            $parameter->setSchema($schema);
            $operation->addParameter($parameter);

            foreach ($refClass->getProperties() as $refProp) {
                $propName = $refProp->getName();
                $propertyNameNormalized = strtolower(preg_replace('/[A-Z]/', '_\\0', $propName));
                $annotations = $this->getAnnotationParser()->getAnnotations($refProp->getDocComment());
                if (!array_key_exists($this->annotationName, $annotations)) {
                    continue;
                }

                $schema->addProperty($propertyNameNormalized, $this->buildBodyProperty($annotations['api']));
            }
        } else {
            foreach ($refClass->getProperties() as $refProp) {
                $propName = $refProp->getName();
                $propertyNameNormalized = strtolower(preg_replace('/[A-Z]/', '_\\0', $propName));
                $annotations = $this->getAnnotationParser()->getAnnotations($refProp->getDocComment());
                if (!array_key_exists($this->annotationName, $annotations)) {
                    continue;
                }

                $operation->addParameter($this->buildQueryParameter($propertyNameNormalized, $annotations['api']));
            }
        }
    }

    /**
     * @param string $name
     * @param array  $apiData
     *
     * @return Parameter
     */
    private function buildQueryParameter(string $name, array $apiData): Parameter
    {
        $info = new ParameterGeneralInfo();
        $info->setType($apiData['type']);
        $info->setCollectionFormat($apiData['collectionFormat'] ?? null);
        if (array_key_exists('items', $apiData)) {
            $info->setItems(new Items($apiData['items']));
        }

        $parameter = new Parameter(Parameter::IN_QUERY);
        $parameter->setName($name);
        $parameter->setRequired($apiData['required'] ?? false);
        $parameter->setDescription($apiData['description'] ?? '');
        $parameter->setExample($apiData['example'] ?? '');
        $parameter->setGeneralInfo($info);

        return $parameter;
    }

    /**
     * @param array $apiData
     *
     * @return Schema
     */
    private function buildBodyProperty(array $apiData): Schema
    {
        $schema = new Schema($apiData['type'] ?? 'string');
        $schema->setExample($apiData['example'] ?? null);
        $schema->setDescription($apiData['description'] ?? null);
        $schema->setMaximum($apiData['max'] ?? null);
        $schema->setMinimum($apiData['min'] ?? null);
        $schema->setRequired($apiData['required'] ?? false);
        if (!empty($apiData['enum'])) {
            $schema->setEnum($apiData['enum']);
        }

        return $schema;
    }
}
