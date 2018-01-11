<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation\FormTypeBuilder;

use PhpSolution\SwaggerUIGen\Component\Model\Parameter;
use PhpSolution\SwaggerUIGen\Component\Model\Schema;
use Symfony\Component\Form\FormInterface;

/**
 * BodyBuilder
 */
class BodyBuilder extends AbstractBuilder
{
    /**
     * {@inheritdoc}
     */
    public function build(): void
    {
        $this->operation->setConsumes([self::CONSUMES_JSON]);
        $this->operation->addParameter($this->buildBodyParameterModel());
    }

    /**
     * @return Parameter
     */
    private function buildBodyParameterModel(): Parameter
    {
        $form = $this->context->getRootForm();
        $config = $form->getConfig();

        $parameter = new Parameter(Parameter::IN_BODY, $this->getParameterName($form));
        $parameter->setDescription($config->getOption('label'));
        $parameter->setName(Parameter::IN_BODY);
        $parameter->setRequired(true);

        $schema = new Schema('object');
        $parameter->setSchema($schema);

        foreach ($form as $childForm) {
            $this->buildProperty($childForm, $schema);
        }

        return $parameter;
    }


    /**
     * @param FormInterface $form
     * @param Schema        $schema
     */
    private function buildProperty(FormInterface $form, Schema $schema)
    {
        if (!$this->isIgnoredFormType($form)) {
            $property = new Schema($this->getParameterType($form));
            $property->setFormat($this->getParameterFormat($form));
            $property->setDescription($form->getConfig()->getOption('label'));
            $this->validatorBuilder->buildFormParameter($property, $form, $this->context);
            $schema->addProperty($this->getPropertyName($form), $property);
        } else {
            if ($this->isCollectionFormType($form)) {
                $property = new Schema('array');
                $property->setItems($this->buildItemList($form));
            } else {
                $property = new Schema('object');
            }

            $schema->addProperty($this->getPropertyName($form), $property);
            $schema = $property;
        }

        /* @var $childForm FormInterface */
        foreach ($form as $childForm) {
            $this->buildProperty($childForm, $schema);
        }
    }

    /**
     * @param FormInterface $form
     *
     * @return Schema
     */
    private function buildItemList(FormInterface $form): Schema
    {
        $schema = new Schema('object');

        $prototype = $form->getConfig()->getAttribute('prototype', null);
        if ($prototype instanceof FormInterface) {
            $prototype->setParent($form);
            foreach ($prototype as $childForm) {
                $this->buildProperty($childForm, $schema);
            }
        }

        return $schema;
    }
}