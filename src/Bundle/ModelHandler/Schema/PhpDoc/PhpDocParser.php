<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Schema\PhpDoc;

/**
 * Class PhpDocParser
 */
class PhpDocParser implements PhpDocParserInterface
{
    private const DEFAULT_TYPE = 'string';
    private const PRIMITIVE_TYPES = ['DateTime'];
    private const TYPE_TRANSFORMER = [
        'datetime' => 'string',
        'bool'     => 'boolean',
        'float'    => 'number',
        'dateTime' => 'string',
        'date'     => 'string',
    ];
    /**
     * @var SimpleAnnotationParser
     */
    private $annotationParser;
    /**
     * @var string
     */
    private $annotationName;

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
     * @param string $className
     *
     * @return array
     */
    public function parse(string $className): array
    {
        $result = [];
        $refClass = new \ReflectionClass($className);
        foreach ($refClass->getProperties() as $refProp) {
            $propName = $refProp->getName();
            $propertyNameNormalized = strtolower(preg_replace('/[A-Z]/', '_\\0', $propName));
            $annotations = $this->getAnnotationParser()->getAnnotations($refProp->getDocComment());

            if (!array_key_exists($this->annotationName, $annotations)) {
                continue;
            }

            $dataType = $this->buildDataType($annotations[$this->annotationName], $propName);
            if (false === $dataType['primitive'] && isset($dataType['class'])) {
                $visited[] = $dataType['class'];
                $children = $this->parse($dataType['class']);
                if ($dataType['inline']) {
                    $result = array_merge($result, $children);
                } else {
                    $dataType['children'] = $children;
                }
            }

            $result[$propertyNameNormalized] = $dataType;
        }

        return $result;
    }

    /**
     * @param array  $annotation
     * @param string $field
     *
     * @return array
     */
    private function buildDataType(array $annotation, string $field): array
    {
        $type = $annotation['type'] ?? self::DEFAULT_TYPE;
        $isPrimitive = $this->isPrimitive($type);
        $dataType = [
            'name'        => $field,
            'required'    => $annotation['required'] ?? false,
            'description' => $annotation['description'] ?? null,
            'primitive'   => $isPrimitive,
            'type'        => $type,
            'inline'      => $annotation['inline'] ?? false,
            'enum'        => array_key_exists('enum', $annotation) ? json_decode($annotation['enum']) : null,
            'pattern'     => null,
            'class'       => null,
            'format'      => null,
            'children'    => null,
            'example'     => $annotation['example'] ?? null,
        ];

        if (isset($annotation['items'])) {
            $dataType['items']['type'] = $annotation['items'];
        }

        if (array_key_exists($type, self::TYPE_TRANSFORMER)) {
            $dataType['type'] = self::TYPE_TRANSFORMER[$type];
            $dataType['format'] = $type;
        }

        if (!$isPrimitive && class_exists(str_replace('[]', '', $type))) {
            $isArray = strpos($type, '[]') !== false;
            $className = str_replace('[]', '', $type);
            $exp = explode("\\", $className);
            $ref = '#/definitions/' . array_pop($exp);
            if ($isArray) {
                $dataType = array_merge($dataType, [
                    'type'  => strpos($type, '[]') === false ? 'object' : 'array',
                    'items' => ['type' => 'object', '$ref' => $ref],
                ]);
            } else {
                $dataType = array_merge($dataType, [
                    'type' => 'object',
                    '$ref' => $ref
                ]);
            }
        }

        return $dataType;
    }

    /**
     * @return SimpleAnnotationParser
     */
    private function getAnnotationParser(): SimpleAnnotationParser
    {
        return $this->annotationParser ?: $this->annotationParser = new SimpleAnnotationParser();
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    private function isPrimitive(string $type): bool
    {
        return in_array($type, self::PRIMITIVE_TYPES) || strpos($type, '\\') === false;
    }
}