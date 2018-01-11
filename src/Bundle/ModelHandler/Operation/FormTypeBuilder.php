<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation;

use PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation\FormTypeBuilder\BodyBuilder;
use PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation\FormTypeBuilder\Context;
use PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation\FormTypeBuilder\FormBuilder;
use PhpSolution\SwaggerUIGen\Component\Model\Operation;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

/**
 * FormTypeBuilder
 */
class FormTypeBuilder implements OperationBuilderInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var RegistryInterface $doctrine
     */
    private $doctrine;
    /**
     * @var FormValidatorBuilder
     */
    private $validatorBuilder;
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * FormTypeBuilder constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param FormValidatorBuilder $validatorBuilder
     * @param RegistryInterface    $doctrine
     * @param RouterInterface      $router
     */
    public function __construct(FormFactoryInterface $formFactory, FormValidatorBuilder $validatorBuilder,
                                RegistryInterface $doctrine, RouterInterface $router)
    {
        $this->formFactory = $formFactory;
        $this->validatorBuilder = $validatorBuilder;
        $this->doctrine = $doctrine;
        $this->router = $router;
    }

    /**
     * @param Operation $operation
     * @param array     $generalConfig
     */
    public function build(Operation $operation, array $generalConfig): void
    {
        if (!isset($generalConfig['request']['form_class'])) {
            return;
        }

        $config = $generalConfig['request'];
        $form = $this->formFactory->create($config['form_class'], null, $config['form_options'] ?? []);

        $methods = $this->router->getRouteCollection()->get($generalConfig['route'])->getMethods();
        $context = new Context($form, $methods);

        array_key_exists('in', $config) &&
        'body' === $config['in'] &&
        Request::METHOD_GET !== $context->getHttpMethod()
            ? (new BodyBuilder($operation, $context, $this->validatorBuilder, $this->doctrine))->build()
            : (new FormBuilder($operation, $context, $this->validatorBuilder, $this->doctrine))->build();
    }
}
