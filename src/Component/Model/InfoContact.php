<?php

namespace PhpSolution\SwaggerUIGen\Component\Model;

use PhpSolution\SwaggerUIGen\Component\Model\Common\NameOptionalTrait;
use PhpSolution\SwaggerUIGen\Component\Model\Common\UrlTrait;

/**
 * Class InfoContact
 *
 * @package PhpSolution\SwaggerUIGen\Component\Model
 */
class InfoContact
{
    use NameOptionalTrait, UrlTrait;

    /**
     * @var string|null
     */
    private $email;

    /**
     * @return null|string
     */
    public function getEmail():? string
    {
        return $this->email;
    }

    /**
     * @param null|string $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }
}