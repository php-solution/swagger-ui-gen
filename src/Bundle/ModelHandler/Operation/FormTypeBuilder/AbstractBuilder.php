<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation\FormTypeBuilder;

use PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation\FormValidatorBuilder;
use PhpSolution\SwaggerUIGen\Component\Model\Operation;
use PhpSolution\SwaggerUIGen\Component\Model\ParameterGeneralInfo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormInterface;

/**
 * AbstractBuilder
 */
abstract class AbstractBuilder
{
    protected const CONSUMES_FORM = 'application/x-www-form-urlencoded';
    protected const CONSUMES_MULTIPART = 'multipart/form-data';
    protected const CONSUMES_JSON = 'application/json';

    private $ignoredFormTypes = [
        Type\CollectionType::class,
        Type\ButtonType::class,
        Type\RepeatedType::class,
    ];

    private $collectionTypes = [
        Type\CollectionType::class,
    ];

    private $typeFormMap = [
        ParameterGeneralInfo::TYPE_BOOLEAN => [
            Type\CheckboxType::class,
        ],
        ParameterGeneralInfo::TYPE_INTEGER => [
            Type\IntegerType::class,
            EntityType::class,
        ],
        ParameterGeneralInfo::TYPE_NUMBER => [
            Type\NumberType::class
        ],
        ParameterGeneralInfo::TYPE_FILE => [
            Type\FileType::class
        ],
        ParameterGeneralInfo::TYPE_ARRAY => [
            Type\ChoiceType::class
        ],
    ];

    private const INTL_DATE_TRANSFORM = [
        \IntlDateFormatter::NONE => '',
        \IntlDateFormatter::FULL => "EEEE, MMMM d, y 'at' h:mm:ss a zzzz",
        \IntlDateFormatter::LONG => "MMMM d, y 'at' h:mm:ss a z",
        \IntlDateFormatter::SHORT => 'M/d/yy, h:mm a',
        \IntlDateFormatter::MEDIUM => 'MMM d, y, h:mm:ss a',
    ];

    /**
     * @var Operation
     */
    protected $operation;
    /**
     * @var Context
     */
    protected $context;
    /**
     * @var FormValidatorBuilder
     */
    protected $validatorBuilder;
    /**
     * @var RegistryInterface
     */
    private $doctrine;

    /**
     * @param Operation            $operation
     * @param Context              $context
     * @param FormValidatorBuilder $validatorBuilder
     * @param RegistryInterface    $doctrine
     */
    public function __construct(Operation $operation, Context $context, FormValidatorBuilder $validatorBuilder, RegistryInterface $doctrine)
    {
        $this->operation = $operation;
        $this->context = $context;
        $this->validatorBuilder = $validatorBuilder;
        $this->doctrine = $doctrine;
    }

    /**
     * Build operation based on rootForm
     */
    abstract public function build(): void;

    /**
     * @param FormInterface $form
     *
     * @return string
     */
    protected function getParameterName(FormInterface $form): string
    {
        return str_replace('__name__', '', $form->createView()->vars['full_name']);
    }

    /**
     * @param FormInterface $form
     *
     * @return string
     */
    protected function getPropertyName(FormInterface $form): string
    {
        return $form->createView()->vars['name'];
    }

    /**
     * @param FormInterface $form
     *
     * @return bool
     */
    protected function isIgnoredFormType(FormInterface $form): bool
    {
        if ($form->count() > 0) {
            return true;
        }

        $formInnerType = $form->getConfig()->getType()->getInnerType();
        foreach ($this->ignoredFormTypes as $type) {
            if ($formInnerType instanceof $type) {
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
    protected function isCollectionFormType(FormInterface $form): bool
    {
        $formInnerType = $form->getConfig()->getType()->getInnerType();
        foreach ($this->collectionTypes as $type) {
            if ($formInnerType instanceof $type) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param FormInterface $form
     *
     * @return string
     */
    protected function getParameterType(FormInterface $form): string
    {
        $formType = $form->getConfig()->getType();
        $formInnerType = $formType->getInnerType();
        // It is necessary because of symfony forms internal inheritance
        $formParentInnerType = $formType->getParent()->getInnerType();

        foreach ($this->typeFormMap as $parameterType => $mapFormClasses) {
            foreach ($mapFormClasses as $mapFormClass) {
                if ($formInnerType instanceof $mapFormClass || $formParentInnerType instanceof $mapFormClass) {
                    return $parameterType;
                }
            }
        }

        return ParameterGeneralInfo::TYPE_STRING;
    }

    /**
     * @param FormInterface $form
     *
     * @return null|string
     */
    protected function getParameterFormat(FormInterface $form):? string
    {
        $option = $form->getConfig()->getOptions();
        $optionFormat = $option['format'] ?? null;
        if (is_int($optionFormat)) {
            $optionFormat = self::INTL_DATE_TRANSFORM[$optionFormat] ?? '';
        }

        if ($form->getParent() && $form->getParent()->getConfig()->getOption('data_class')) {
            $metadata = $this->doctrine->getManager()->getClassMetadata($form->getParent()->getConfig()->getOption('data_class'));

            $name = $this->getPropertyName($form);
            if ($metadata->hasField($name)) {
                $fieldMetadata = $metadata->getFieldMapping($name);

                switch ($fieldMetadata['type']) {
                    case 'integer':
                        $optionFormat = 'int64';
                        break;
                    case 'float':
                        $optionFormat = 'float';
                        break;
                }
            }
        }

        return $optionFormat;
    }
}