<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation;

use Doctrine\Bundle\DoctrineBundle\Registry;
use PhpSolution\SwaggerUIGen\Component\Model\Items;
use PhpSolution\SwaggerUIGen\Component\Model\Operation;
use PhpSolution\SwaggerUIGen\Component\Model\Parameter;
use PhpSolution\SwaggerUIGen\Component\Model\ParameterGeneralInfo;
use PhpSolution\SwaggerUIGen\Component\Model\Schema;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
    private const CONSUMES_MULTIPART = 'multipart/form-data';
    private const CONSUMES_JSON = 'application/json';
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
     * @var Registry $doctrine
     */
    private $doctrine;

    /**
     * @var array
     */
    private $typeFormMap = [
        ParameterGeneralInfo::TYPE_BOOLEAN => [
            'Symfony\Component\Form\Extension\Core\Type\CheckboxType',
        ],
        ParameterGeneralInfo::TYPE_INTEGER => [
            'Symfony\Component\Form\Extension\Core\Type\IntegerType',
            'Symfony\Bridge\Doctrine\Form\Type\EntityType',
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
     * @var string
     */
    private $formMethod;

    /**
     * @var bool
     */
    private $hasFileType = false;

    /**
     * FormTypeBuilder constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param FormValidatorBuilder $validatorBuilder
     * @param Registry             $doctrine
     */
    public function __construct(FormFactoryInterface $formFactory, FormValidatorBuilder $validatorBuilder, Registry $doctrine)
    {
        $this->formFactory = $formFactory;
        $this->validatorBuilder = $validatorBuilder;
        $this->doctrine = $doctrine;
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
        $this->formMethod = $form->getConfig()->getMethod();
        $this->hasFileType = false;

        if (array_key_exists('in', $config) && 'body' === $config['in'] && in_array($this->formMethod, ['POST', 'PUT', 'PATCH'])) {
            $operation->setConsumes([self::CONSUMES_JSON]);
            $operation->addParameter($this->buildObjectModel($form));
        } else {
            foreach ($this->buildParameterModelList($form, new \ArrayObject()) as $parameterModel) {
                $operation->addParameter($parameterModel);
            }

            if ($this->hasFileType) {
                $operation->setConsumes([self::CONSUMES_MULTIPART]);
            } else {
                $operation->setConsumes([self::CONSUMES]);
            }
        }
    }

    /**
     * @param FormInterface $form
     * @param bool          $isCollection
     *
     * @return Parameter
     */
    private function buildObjectModel(FormInterface $form, bool $isCollection = false): Parameter
    {
        $config = $form->getConfig();

        $parameter = new Parameter(Parameter::IN_BODY, $this->getParameterName($form));
        $parameter->setDescription($config->getOption('label'));
        $parameter->setName(Parameter::IN_BODY);
        $parameter->setRequired(true);

        $schema = new Schema('object');
        $parameter->setSchema($schema);

        /* @var $childForm FormInterface */
        foreach ($form as $childForm) {
            $this->buildPropertyList($childForm, $schema, $isCollection);
        }

        return $parameter;
    }

    /**
     * @param FormInterface $form
     * @param Schema        $schema
     * @param bool          $isCollection
     */
    private function buildPropertyList(FormInterface $form, Schema $schema, bool $isCollection = false)
    {
        $config = $form->getConfig();

        if (!$this->isIgnoredFormType($form)) {
            $property = new Schema($this->getParameterType($form));
            $property->setFormat($this->getParameterFormat($form));
            $property->setDescription($config->getOption('label'));
            $schema->addProperty($this->getPropertyName($form), $property);

            if ($this->validatorBuilder->isRequired($form)) {
                $schema->addRequired($this->getPropertyName($form));
            }
        } else {
            if ($this->isTypeCollection($config->getType()->getInnerType())) {
                $property = new Schema('array');
                $property->setItems($this->buildItemList($form));
                $schema->addProperty($this->getPropertyName($form), $property);
            } else {
                if ('__name__' !== $this->getPropertyName($form)) {
                    $property = new Schema('object');
                    $schema->addProperty($this->getPropertyName($form), $property);
                }
            }

            if (isset($property)) {
                $schema = $property;
            }
        }

        /* @var $childForm FormInterface */
        foreach ($form as $childForm) {
            $this->buildPropertyList($childForm, $schema, $isCollection);
        }
    }

    /**
     * @param FormInterface $form
     *
     * @return Schema
     */
    private function buildItemList(FormInterface $form): Schema
    {
        $config = $form->getConfig();

        $schema = new Schema('object');

        $prototype = $config->getAttribute('prototype', null);
        if ($prototype instanceof FormInterface) {
            $prototype->setParent($form);
            $this->buildPropertyList($prototype, $schema, true);
        }

        return $schema;
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
            if ('Symfony\Component\HttpFoundation\File\File' === $config->getDataClass()) {
                $this->hasFileType = true;
            }

            $parameterInfo = new ParameterGeneralInfo();
            $parameterInfo->setType($this->getParameterType($form));
            $parameterInfo->setFormat($this->getParameterFormat($form));
            $parameterInfo->setCollectionFormat(array_key_exists('multiple', $options) && $options['multiple'] ? 'multi' : null);
            $parameterInfo->setEnum($this->getParameterEnum($form));

            $parameter = new Parameter($this->formMethod === 'GET' ? Parameter::IN_QUERY : Parameter::IN_FORM_DATA, $this->getParameterName($form));
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
            if (is_subclass_of($form, $type) || $form instanceof $type) {
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

        if ($form->getParent()->getConfig()->getOption('data_class')) {
            $metadata = $this->doctrine->getManager()->getClassMetadata($form->getParent()->getConfig()->getOption('data_class'));

            $name = $this->getPropertyName($form);
            if ($metadata->hasField($name)) {
                $fieldMetadata = $metadata->getFieldMapping($name);

                if ('string' !== $fieldMetadata['type']) {
                    switch ($fieldMetadata['type']) {
                        case 'integer':
                            $optionFormat = 'int32';
                            break;
                        case 'float':
                            $optionFormat = 'float';
                            break;
                    }
                }
            }
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
                if (is_subclass_of($formClass, $mapFormClass) || is_subclass_of($parentFormClass, $mapFormClass) || $formClass === $mapFormClass) {
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
     * @return string
     */
    private function getPropertyName(FormInterface $form): string
    {
        return $form->createView()->vars['name'];
    }

    /**
     * @param FormInterface $form
     *
     * @return array
     */
    private function getParameterEnum(FormInterface $form):? array
    {
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
