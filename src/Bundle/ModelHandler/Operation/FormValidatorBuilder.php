<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation;

use PhpSolution\SwaggerUIGen\Component\Model\DataTypeFormat;
use PhpSolution\SwaggerUIGen\Component\Model\Parameter;
use PhpSolution\SwaggerUIGen\Component\Model\Schema;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface;
use Symfony\Component\Validator\Mapping\PropertyMetadataInterface;
use Symfony\Component\Validator\Constraints;

/**
 * Class FormValidatorBuilder
 *
 * @package PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation
 */
class FormValidatorBuilder
{
    /**
     * @var MetadataFactoryInterface
     */
    private $classMetadataFactory;

    /**
     * ValidatorBuilder constructor.
     *
     * @param MetadataFactoryInterface $classMetadataFactory
     */
    public function __construct(MetadataFactoryInterface $classMetadataFactory)
    {
        $this->classMetadataFactory = $classMetadataFactory;
    }

    /**
     * @param mixed     $parameter
     * @param FormInterface $form
     */
    public function buildFormParameter($parameter, FormInterface $form): void
    {
        $builders = $this->getConstraintBuilders();
        foreach ($this->getFormConstraints($form) as $constraint) {
            $constraintClass = get_class($constraint);
            foreach ($builders as $builderConstraintClass => $builder) {
                if ($constraintClass === $builderConstraintClass || is_subclass_of($constraint, $constraintClass)) {
                    call_user_func_array($builder, [$parameter, $constraint]);
                }
            }
        }
    }

    /**
     * @param FormInterface $form
     *
     * @return bool
     */
    public function isRequired(FormInterface $form): bool
    {
        $constraints = $this->getFormConstraints($form);

        foreach ($constraints as $constraint) {
            if (is_a($constraint, 'Symfony\Component\Validator\Constraints\NotBlank')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    private function getConstraintBuilders(): array
    {
        return [
            Constraints\NotBlank::class => function ($parameter) {
                /** @var $parameter Parameter|Schema */
                $parameter->addDescription('Not blank');
            },
            Constraints\Regex::class => function ($parameter, Constraints\Regex $constraint) {
                /** @var $parameter Parameter|Schema */
                $parameter->addDescription(sprintf('pattern: "%s"', $constraint->pattern));
                $parameter->getGeneralInfo()->setPattern($constraint->pattern);
            },
            Constraints\Choice::class => function ($parameter, Constraints\Choice $constraint) {
                /** @var $parameter Parameter|Schema */
                $parameter->addDescription(sprintf('enum: "%s"', print_r($constraint->choices, true)));
                $parameter->getGeneralInfo()->setEnum($constraint->choices);
            },
            Constraints\Length::class => function ($parameter, Constraints\Length $constraint) {
                if (!is_null($constraint->max)) {
                    /** @var $parameter Parameter|Schema */
                    $parameter->addDescription(sprintf('min: "%s"', $constraint->min));
                    $parameter->getGeneralInfo()->setMinimum($constraint->min);
                }
                if (!is_null($constraint->max)) {
                    /** @var $parameter Parameter|Schema */
                    $parameter->addDescription(sprintf('max: "%s"', $constraint->max));
                    $parameter->getGeneralInfo()->setMaximum($constraint->max);
                }
            },
            Constraints\Type::class => function ($parameter, Constraints\Type $constraint) {
                /** @var $parameter Parameter|Schema */
                $parameter->addDescription(sprintf('type: "%s"', $constraint->type));
                $parameter->getGeneralInfo()->setType($constraint->type);
            },
            Constraints\Date::class => function ($parameter) {
                /** @var $parameter Parameter|Schema */
                $parameter->addDescription(sprintf('format: "%s"', DataTypeFormat::FORMAT_DATE));
                $parameter->getGeneralInfo()->setFormat(DataTypeFormat::FORMAT_DATE);
            },
            Constraints\DateTime::class => function ($parameter) {
                /** @var $parameter Parameter|Schema */
                $parameter->addDescription(sprintf('format: "%s"', DataTypeFormat::FORMAT_DATETIME));
                $parameter->getGeneralInfo()->setFormat(DataTypeFormat::FORMAT_DATETIME);
            },
            Constraints\Time::class => function ($parameter) {
                /** @var $parameter Parameter|Schema */
                $parameter->addDescription(sprintf('format: "%s"', DataTypeFormat::FORMAT_TIME));
                $parameter->getGeneralInfo()->setFormat(DataTypeFormat::FORMAT_TIME);
            },
            Constraints\Email::class => function ($parameter) {
                /** @var $parameter Parameter|Schema */
                $parameter->addDescription(sprintf('format: "%s"', DataTypeFormat::FORMAT_EMAIL));
                $parameter->getGeneralInfo()->setFormat(DataTypeFormat::FORMAT_EMAIL);
            },
            Constraints\Ip::class => function ($parameter, Constraints\Ip $constraint) {
                if (Constraints\Ip::V4 === $constraint->version) {
                    /** @var $parameter Parameter|Schema */
                    $parameter->addDescription(sprintf('format: "%s"', DataTypeFormat::FORMAT_IPADDRESS));
                    $parameter->getGeneralInfo()->setFormat(DataTypeFormat::FORMAT_IPADDRESS);
                } elseif (Constraints\Ip::V6 === $constraint->version) {
                    /** @var $parameter Parameter|Schema */
                    $parameter->addDescription(sprintf('format: "%s"', DataTypeFormat::FORMAT_IPV6));
                    $parameter->getGeneralInfo()->setFormat(DataTypeFormat::FORMAT_IPV6);
                }
            },
            Constraints\Url::class => function ($parameter) {
                /** @var $parameter Parameter|Schema */
                $parameter->addDescription('Valid URL');
            },
            Constraints\GreaterThanOrEqual::class => function ($parameter, Constraints\GreaterThanOrEqual $constraint) {
                /** @var $parameter Parameter|Schema */
                $parameter->addDescription(sprintf('min: "%s"', $constraint->value));
            },
            Constraints\LessThanOrEqual::class => function ($parameter, Constraints\LessThanOrEqual $constraint) {
                /** @var $parameter Parameter|Schema */
                $parameter->addDescription(sprintf('max: "%s"', $constraint->value));
            },
        ];
    }

    /**
     * @param FormInterface $form
     *
     * @return array|Constraint[]
     */
    private function getFormConstraints(FormInterface $form): array
    {
        $formConfig = $form->getConfig();
        $constraints = $formConfig->getOption('constraints');
        $groups = self::getValidationGroups($form);
        $parentForm = $form->getParent();

        if ($parentForm instanceof FormInterface && $form->getName() && $parentForm->getConfig()->getOption('data_class')) {
            $parentDataClass = $parentForm->getConfig()->getOption('data_class');
            /* @var $validationMetadata ClassMetadata */
            $validationMetadata = $this->classMetadataFactory->getMetadataFor($parentDataClass);
            $propertyMetadata = $validationMetadata->getPropertyMetadata($form->getName());

            if (!$propertyMetadata && $form->getPropertyPath()->getLength()) {
                foreach ($form->getPropertyPath()->getElements() as $element) {
                    $propertyMetadata = array_merge($propertyMetadata, $validationMetadata->getPropertyMetadata($element));
                }
            }

            $constraints = [];
            foreach ($groups as $group) {
                if (is_array($propertyMetadata)) {
                    /* @var $propertyMetadata PropertyMetadataInterface[] */
                    foreach ($propertyMetadata as $item) {
                        $constraints = array_merge($constraints, $item->findConstraints($group));
                    }
                } else {
                    $constraints = array_merge($constraints, $propertyMetadata->findConstraints($group));
                }
            }
        }

        return $constraints;
    }

    /**
     * @see \Symfony\Component\Form\Extension\Validator\Constraints\FormValidator::getValidationGroups
     *
     * @param FormInterface $form
     *
     * @return array
     */
    private static function getValidationGroups(FormInterface $form): array
    {
        $clickedButton = null;
        if (method_exists($form, 'getClickedButton')) {
            $clickedButton = $form->getClickedButton();
        }
        if (null !== $clickedButton) {
            $groups = $clickedButton->getConfig()->getOption('validation_groups');
            if (null !== $groups) {
                return self::resolveValidationGroups($groups, $form);
            }
        }

        do {
            $groups = $form->getConfig()->getOption('validation_groups');
            if (null !== $groups) {
                return self::resolveValidationGroups($groups, $form);
            }

            $form = $form->getParent();
        } while (null !== $form);

        return [Constraint::DEFAULT_GROUP];
    }

    /**
     * @param               $groups
     * @param FormInterface $form
     *
     * @return array|mixed
     */
    private static function resolveValidationGroups($groups, FormInterface $form)
    {
        if (!is_string($groups) && is_callable($groups)) {
            $groups = call_user_func($groups, $form);
        }
        if ($groups instanceof GroupSequence) {
            $groups = $groups->groups;
        }

        return (array) $groups;
    }
}
