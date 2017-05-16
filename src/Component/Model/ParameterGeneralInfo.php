<?php

namespace PhpSolution\SwaggerUIGen\Component\Model;

/**
 * Class ParameterGeneralInfo
 *
 * @package PhpSolution\SwaggerUIGen\Component\Model
 */
class ParameterGeneralInfo extends Items
{
    public const TYPES = ['string', 'number', 'integer', 'boolean', 'array', 'file'];
    public const TYPES_TRANSFORMER = [
        'boolean' => 'boolean',
        'integer' => 'integer',
        'double' => 'number',
        'string' => 'string',
        'array' => 'array',
        'object' => 'array',
        'resource' => 'file',
        'NULL' => 'string',
        'unknown type' => 'string'
    ];
    public const TYPE_STRING = 'string';
    public const TYPE_NUMBER = 'number';
    public const TYPE_INTEGER = 'integer';
    public const TYPE_BOOLEAN = 'boolean';
    public const TYPE_ARRAY = 'array';
    public const TYPE_FILE = 'file';
}