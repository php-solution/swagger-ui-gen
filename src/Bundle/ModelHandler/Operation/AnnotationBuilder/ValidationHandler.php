<?php
declare(strict_types=1);

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation\AnnotationBuilder;

use PhpSolution\SwaggerUIGen\Component\Model\Parameter;
use PhpSolution\SwaggerUIGen\Component\Model\Schema;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * TODO: implement validation groups, add all constraints
 */
class ValidationHandler
{
    /**
     * @var ClassMetadata|null
     */
    private $classMetadata;

    /**
     * @param ClassMetadata|null $classMetadata
     */
    public function __construct(ClassMetadata $classMetadata = null)
    {
        $this->classMetadata = $classMetadata;
    }

    /**
     * @param Parameter $parameter
     * @param string    $name
     */
    public function handleParameter(Parameter $parameter, string $name): void
    {
        if (!$this->classMetadata instanceof ClassMetadata || !$this->classMetadata->hasPropertyMetadata($name)) {
            return;
        }

        $builders = $this->getConstraintBuilders();
        foreach ($this->classMetadata->getPropertyMetadata($name) as $propertyMetadata) {
            foreach ($propertyMetadata->getConstraints() as $constraint) {
                $constraintClass = get_class($constraint);
                foreach ($builders as $builderConstraintClass => $builder) {
                    if ($constraintClass === $builderConstraintClass || is_subclass_of($constraint, $constraintClass)) {
                        call_user_func_array($builder, [$parameter, $constraint]);
                    }
                }
            }
        }
    }

    /**
     * @return array
     */
    private function getConstraintBuilders(): array
    {
        return [
            Constraints\Range::class => function (Parameter $property, Constraints\Range $constraint) {
                /* @var Parameter|Schema $property */
                $min = null === $constraint->min ? '∞' : $constraint->min;
                $max = null === $constraint->max ? '∞' : $constraint->max;

                $property->addDescription(sprintf('<b>Range:</b> %s - %s', $min, $max));
            },
        ];
    }
}
