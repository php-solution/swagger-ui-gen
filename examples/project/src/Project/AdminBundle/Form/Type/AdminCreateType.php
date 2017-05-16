<?php

namespace Project\AdminBundle\Form\Type;

use PhpSolution\Doctrine\Form\Type\CachedEntityType;
use Project\AdminBundle\Entity\AdminRole;
use Project\AdminBundle\Lib\AdminModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AdminCreateType
 *
 * @package Project\AdminBundle\Form\Type
 */
class AdminCreateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, ['label' => 'Email for Admin'])
            ->add('roles', CachedEntityType::class, ['class' => AdminRole::class, 'multiple' => true]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => AdminModel::class]);
    }
}