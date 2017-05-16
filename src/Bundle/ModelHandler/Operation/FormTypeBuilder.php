<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation;

use PhpSolution\SwaggerUIGen\Component\Model\Items;
use PhpSolution\SwaggerUIGen\Component\Model\Operation;
use PhpSolution\SwaggerUIGen\Component\Model\Parameter;
use PhpSolution\SwaggerUIGen\Component\Model\ParameterGeneralInfo;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;

/**
 * Class FormTypeBuilder
 *
 * @package PhpSolution\SwaggerUIGen\Bundle\ModelHandler\OperationBuilder
 */
class FormTypeBuilder implements OperationBuilderInterface
{
    private const CONSUMES = 'application/x-www-form-urlencoded';
    private const INTL_DATE_TRANSFORM = [
        \IntlDateFormatter::NONE => '',
        \IntlDateFormatter::FULL => "EEEE, MMMM d, y 'at' h:mm:ss a zzzz",
        \IntlDateFormatter::LONG => "MMMM d, y 'at' h:mm:ss a z",
        \IntlDateFormatter::SHORT => 'M/d/yy, h:mm a',
        \IntlDateFormatter::MEDIUM => 'MMM d, y, h:mm:ss a',
    ];
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var array
     */
    private $typeFormMap = [
        ParameterGeneralInfo::TYPE_BOOLEAN => [
            'Symfony\Component\Form\Extension\Core\Type\CheckboxType',
        ],
        ParameterGeneralInfo::TYPE_INTEGER => [
            'Symfony\Component\Form\Extension\Core\Type\IntegerType'
        ],
        ParameterGeneralInfo::TYPE_NUMBER => [
            'Symfony\Component\Form\Extension\Core\Type\NumberType'
        ],
        ParameterGeneralInfo::TYPE_FILE => [
            'Symfony\Component\Form\Extension\Core\Type\FileType'
        ],
        ParameterGeneralInfo::TYPE_ARRAY => [
            'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
        ],
    ];
    private $collectionTypes = [
        'Symfony\Component\Form\Extension\Core\Type\CollectionType'
    ];
    /**
     * @var array
     */
    private $ignoredFormTypes = [
        'Symfony\Component\Form\Extension\Core\Type\CollectionType',
        'Symfony\Component\Form\Extension\Core\Type\ButtonType',
        'Symfony\Component\Form\Extension\Core\Type\RepeatedType',
    ];
    /**
     * @var FormValidatorBuilder
     */
    private $validatorBuilder;

    /**
     * FormTypeBuilder constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param FormValidatorBuilder $validatorBuilder
     */
    public function __construct(FormFactoryInterface $formFactory, FormValidatorBuilder $validatorBuilder)
    {
        $this->formFactory = $formFactory;
        $this->validatorBuilder = $validatorBuilder;
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
        $operation->setConsumes([self::CONSUMES]);
        $form = $this->formFactory->create($config['form_class'], null, $config['form_options'] ?? []);
        foreach ($this->buildParameterModelList($form, new \ArrayObject()) as $parameterModel) {
            $operation->addParameter($parameterModel);
        }
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
            $parameterInfo = new ParameterGeneralInfo();
            $parameterInfo->setType($this->getParameterType($form));
            $parameterInfo->setFormat($this->getParameterFormat($form));
            $parameterInfo->setCollectionFormat(array_key_exists('multiple', $options) && $options['multiple'] ? 'multi' : null);
            $parameterInfo->setEnum($this->getParameterEnum($form));

            $parameter = new Parameter(Parameter::IN_FORM_DATA, $this->getParameterName($form));
            $parameter->setDescription($config->getOption('label'));
            $parameter->setGeneralInfo($parameterInfo);

            // Build via form type validator
            $this->validatorBuilder->buildFormParameter($parameter, $form);

            if ($isCollection) {
                $items = new Items($parameterInfo->getType());
                if ($parameterInfo->getEnum()) {
                    $subItems = new Items($parameterInfo->getType());
                    $subItems->setEnum($parameterInfo->getEnum());
                    $items->setType('array');
                    $items->setItems($subItems);
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

        // Handle Collection
        if ($this->isTypeCollection($config->getType()->getInnerType())) {
            $prototype = $config->hasAttribute('prototype') ? $config->getAttribute('prototype') : null;
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
     * @param FormTypeInterface $form
     *
     * @return bool
     */
    private function isTypeCollection(FormTypeInterface $form): bool
    {
        foreach ($this->collectionTypes as $type) {
            if (is_subclass_of($form, $type)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param FormInterface $form
     *
     * @return bool
     */
    private function isIgnoredFormType(FormInterface $form): bool
    {
        if ($form->count() > 0) {
            return true;
        }
        $formType = $form->getConfig()->getType();
        $formClass = get_class($formType->getInnerType());
        $parentFormClass = get_class($formType->getParent()->getInnerType());

        foreach ($this->ignoredFormTypes as $ignoredFormType) {
            if (
                $formClass === $ignoredFormType
                || $parentFormClass === $ignoredFormType
                || is_subclass_of($formClass, $ignoredFormType)
                || is_subclass_of($parentFormClass, $ignoredFormType)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param FormInterface $form
     *
     * @return null|string
     */
    private function getParameterFormat(FormInterface $form):? string
    {
        $option = $form->getConfig()->getOptions();
        $optionFormat = $option['format'] ?? null;
        if (is_int($optionFormat)) {
            $optionFormat = self::INTL_DATE_TRANSFORM[$optionFormat] ?? '';
        }

        return $optionFormat;
    }

    /**
     * @param FormInterface $form
     *
     * @return string
     */
    private function getParameterType(FormInterface $form): string
    {
        $formType = $form->getConfig()->getType();
        $formClass = get_class($formType->getInnerType());
        $parentFormClass = get_class($formType->getParent()->getInnerType());

        foreach ($this->typeFormMap as $parameterType => $mapFormClasses) {
            foreach ($mapFormClasses as $mapFormClass) {
                if (is_subclass_of($formClass, $mapFormClass) || is_subclass_of($parentFormClass, $mapFormClass)) {
                    return $parameterType;
                }
            }
        }

        return ParameterGeneralInfo::TYPE_STRING;
    }

    /**
     * @param FormInterface $form
     *
     * @return string
     */
    private function getParameterName(FormInterface $form): string
    {
        return str_replace('__name__', '', $form->createView()->vars['full_name']);
    }

    /**
     * @param FormInterface $form
     *
     * @return array
     */
    private function getParameterEnum(FormInterface $form):? array
    {
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