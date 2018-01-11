<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation\FormTypeBuilder;

use PhpSolution\SwaggerUIGen\Component\Model\Items;
use PhpSolution\SwaggerUIGen\Component\Model\Parameter;
use PhpSolution\SwaggerUIGen\Component\Model\ParameterGeneralInfo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;

/**
 * FormBuilder
 */
class FormBuilder extends AbstractBuilder
{
    /**
     * @var bool
     */
    private $hasFileType = false;

    /**
     * {@inheritdoc}
     */
    public function build(): void
    {
        foreach ($this->buildParameterModelList($this->context->getRootForm(), new \ArrayObject()) as $parameterModel) {
            $this->operation->addParameter($parameterModel);
        }
        $this->operation->setConsumes([$this->hasFileType ? self::CONSUMES_MULTIPART : self::CONSUMES_FORM]);
    }

    /**
     * @param FormInterface $form
     * @param \ArrayObject  $parameterList
     * @param bool          $isCollection
     *
     * @return \ArrayObject
     */
    private function buildParameterModelList(FormInterface $form, \ArrayObject $parameterList, bool $isCollection = false): \ArrayObject
    {
        $config = $form->getConfig();
        $options = $config->getOptions();
        if (!$this->isIgnoredFormType($form)) {
            if (File::class === $config->getDataClass()) {
                $this->hasFileType = true;
            }

            $parameterInfo = new ParameterGeneralInfo();
            $parameterInfo->setType($this->getParameterType($form));

            // array data type can not have format field. see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/2.0.md#dataTypeFormat
            if (ParameterGeneralInfo::TYPE_ARRAY !== $parameterInfo->getType()) {
                $parameterInfo->setFormat($this->getParameterFormat($form));
            }

            $parameterInfo->setCollectionFormat(array_key_exists('multiple', $options) && $options['multiple'] ? 'multi' : null);
            $parameterInfo->setEnum($this->getParameterEnum($form));

            $in = Request::METHOD_GET === $this->context->getHttpMethod() ? Parameter::IN_QUERY : Parameter::IN_FORM_DATA;
            $parameter = new Parameter($in, $this->getParameterName($form));
            $parameter->setDescription($config->getOption('label'));
            $parameter->setGeneralInfo($parameterInfo);

            // Build via form type validator
            $this->validatorBuilder->buildFormParameter($parameter, $form, $this->context);

            /**
             * items is Required if type is "array"
             * https://github.com/OAI/OpenAPI-Specification/blob/master/versions/2.0.md#fixed-fields-7
             */
            if ($isCollection || ParameterGeneralInfo::TYPE_ARRAY === $parameterInfo->getType()) {
                $items = new Items($parameterInfo->getType());
                if ($parameterInfo->getEnum()) {
                    $subItems = new Items($parameterInfo->getType());
                    $subItems->setEnum($parameterInfo->getEnum());

                    if ($isCollection) {
                        $items->setType('array');
                        $items->setItems($subItems);
                    } else {
                        $enum = $parameterInfo->getEnum();
                        $subItems->setType(gettype(array_pop($enum)));
                        $items = $subItems;
                    }

                    $parameterInfo->setEnum(null);
                }
                $parameterInfo->setItems($items);
                $parameterInfo->setType('array');
            } elseif ($parameterInfo->getCollectionFormat() === 'multi' && $parameterInfo->getEnum()) {
                // Build select choices
                $items = new Items($parameterInfo->getType());
                $items->setEnum($parameterInfo->getEnum());
                $parameterInfo->setItems($items);
                $parameterInfo->setType('array');
                $parameterInfo->setEnum(null);
            }

            $parameterList->append($parameter);
        }

        if ($this->isCollectionFormType($form)) {
            $prototype = $config->getAttribute('prototype', null);
            if ($prototype instanceof FormInterface) {
                $prototype->setParent($form);
                $this->buildParameterModelList($prototype, $parameterList, true);
            }
        }

        /* @var $childForm FormInterface */
        foreach ($form as $childForm) {
            $this->buildParameterModelList($childForm, $parameterList, $isCollection);
        }

        return $parameterList;
    }

    /**
     * @param FormInterface $form
     *
     * @return array
     */
    protected function getParameterEnum(FormInterface $form):? array
    {
        // Can't make EntityType enum, because it value can dynamically changed
        if ($form->getConfig()->getType()->getInnerType() instanceof EntityType) {
            return null;
        }
        $formView = $form->createView();
        if (!isset($formView->vars['choices'])) {
            return null;
        }
        $result = [];
        /* @var ChoiceView $choiceView */
        foreach ($formView->vars['choices'] as $choiceView) {
            $result[] = $choiceView->value;
        }

        return $result;
    }
}