<?php

namespace PhpSolution\SwaggerUIGen\Component\Model;

/**
 * Class DataTypeFormat
 *
 * @package PhpSolution\SwaggerUIGen\Component\Model
 */
final class DataTypeFormat
{
    public const FORMATS = ['integer', 'long', 'float', 'double', 'string', 'byte', 'binary', 'boolean', 'date', 'dateTime', 'password'];
    public const FORMAT_IPADDRESS = 'ip-address';
    public const FORMAT_IPV6 = 'ipv6';
    public const FORMAT_EMAIL = 'email';
    public const FORMAT_DATETIME = 'date-time';
    public const FORMAT_DATE = 'date';
    public const FORMAT_TIME = 'time';
}