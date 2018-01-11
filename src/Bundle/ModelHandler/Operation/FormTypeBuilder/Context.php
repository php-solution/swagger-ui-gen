<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation\FormTypeBuilder;

use Symfony\Component\Form\FormInterface;

/**
 * Context
 */
class Context
{
    /**
     * @var FormInterface
     */
    private $rootForm;
    /**
     * @var string
     */
    private $httpMethod;
    /**
     * @var bool
     */
    private $hasFileType;

    /**
     * @param FormInterface $rootForm
     * @param string[]      $methods
     */
    public function __construct(FormInterface $rootForm, array $methods)
    {
        $this->rootForm = $rootForm;
        $this->httpMethod = 1 === count($methods) ? current($methods) : $rootForm->getConfig()->getMethod();
        $this->hasFileType = false;
    }

    /**
     * @return FormInterface
     */
    public function getRootForm(): FormInterface
    {
        return $this->rootForm;
    }

    /**
     * @return string
     */
    public function getHttpMethod(): string
    {
        return $this->httpMethod;
    }

    /**
     * @return bool
     */
    public function isHasFileType(): bool
    {
        return $this->hasFileType;
    }

    /**
     * @param bool $hasFileType
     *
     * @return self
     */
    public function setHasFileType(bool $hasFileType)
    {
        $this->hasFileType = $hasFileType;

        return $this;
    }
}