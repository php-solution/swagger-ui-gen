<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Schema\PhpDoc;

/**
 * Class SimpleAnnotationParser
 */
class SimpleAnnotationParser
{
    const ANNOTATION_REGEX = '/@(\w+)(?:\s*(?:\(\s*)?(.*?)(?:\s*\))?)??\s*(?:\n|\*\/)/';
    const PARAMETER_REGEX = '/(\w+)\s*=\s*(\[[^\]]*\]|"[^"]*"|[^,)]*)\s*(?:,|$)/';

    /**
     * @param string $docComment
     *
     * @return array
     */
    public function getAnnotations(string $docComment): array
    {
        $hasAnnotations = preg_match_all(self::ANNOTATION_REGEX, $docComment, $matches, PREG_SET_ORDER);

        if (!$hasAnnotations) {
            return [];
        }

        $annotations = [];
        foreach ($matches as $annotation) {
            $annotationName = strtolower($annotation[1]);
            $value = true;
            if (isset($annotation[2])) {
                $hasParams = preg_match_all(self::PARAMETER_REGEX, $annotation[2], $params, PREG_SET_ORDER);

                if ($hasParams) {
                    $value = [];
                    foreach ($params as $param) {
                        $value[$param[1]] = $this->parseValue($param[2]);
                    }
                } else {
                    $value = trim($annotation[2]);
                    if ($value == '') {
                        $value = true;
                    } else {
                        $value = $this->parseValue($value);
                    }
                }
            }

            if (isset($annotations[$annotationName])) {
                if (!is_array($annotations[$annotationName])) {
                    $annotations[$annotationName] = [$annotations[$annotationName]];
                }
                $annotations[$annotationName][] = $value;
            } else {
                $annotations[$annotationName] = $value;
            }
        }

        return $annotations;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    private function parseValue($value)
    {
        $value = trim($value);
        if (substr($value, 0, 1) == '[' && substr($value, -1) == ']') {
            $values = explode(',', substr($value, 1, -1));
            $value = [];
            foreach ($values as $v) {
                $value[] = $this->parseValue($v);
            }

            return $value;
        }

        if (substr($value, 0, 1) == '{' && substr($value, -1) == '}') {
            return json_decode($value);
        }

        if (substr($value, 0, 1) == '"' && substr($value, -1) == '"') {
            $value = substr($value, 1, -1);

            return $this->parseValue($value);
        }

        if (in_array(strtolower($value), ['true', 'false'])) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        if (is_numeric($value)) {
            if ((float)$value == (int)$value) {
                return (int)$value;
            } else {
                return (float)$value;
            }
        }

        return $value;
    }

}