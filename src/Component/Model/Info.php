<?php

namespace PhpSolution\SwaggerUIGen\Component\Model;

use PhpSolution\SwaggerUIGen\Component\Model\Common\DescriptionTrait;
use PhpSolution\SwaggerUIGen\Component\Model\Common\TitleRequiredTrait;

/**
 * Class Info
 *
 * @package PhpSolution\SwaggerUIGen\Component\Model
 */
class Info
{
    use TitleRequiredTrait, DescriptionTrait;

    /**
     * @var string
     */
    private $version = '1.0.0';
    /**
     * @var string|null
     */
    private $termsOfService;
    /**
     * @var InfoContact|null
     */
    private $contact;
    /**
     * @var InfoLicense|null
     */
    private $license;

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     *
     * @return Info
     */
    public function setVersion(string $version): Info
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getTermsOfService():? string
    {
        return $this->termsOfService;
    }

    /**
     * @param null|string $termsOfService
     *
     * @return Info
     */
    public function setTermsOfService(?string $termsOfService): Info
    {
        $this->termsOfService = $termsOfService;

        return $this;
    }

    /**
     * @return null|InfoContact
     */
    public function getContact():? InfoContact
    {
        return $this->contact;
    }

    /**
     * @param null|InfoContact $contact
     *
     * @return Info
     */
    public function setContact(?InfoContact $contact): Info
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * @return null|InfoLicense
     */
    public function getLicense():? InfoLicense
    {
        return $this->license;
    }

    /**
     * @param null|InfoLicense $license
     *
     * @return Info
     */
    public function setLicense(?InfoLicense $license): Info
    {
        $this->license = $license;

        return $this;
    }
}