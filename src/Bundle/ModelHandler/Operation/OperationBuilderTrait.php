<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation;

use Symfony\Component\Form\FormInterface;

/**
 * OperationBuilderTrait
 */
trait OperationBuilderTrait
{
    /**
     * @param FormInterface $form
     *
     * @return string
     */
    protected function getFormBaseMethod(FormInterface $form): string
    {
        while (($parent = $form->getParent()) !== null) {
            $form = $parent;
        }

        return $form->getConfig()->getMethod();
    }
}