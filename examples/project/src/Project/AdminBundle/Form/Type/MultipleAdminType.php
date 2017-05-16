<?php

namespace Project\AdminBundle\Form\Type;

use Project\AdminBundle\Lib\AdminListModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MultipleAdminType
 *
 * @package Project\AdminBundle\Form\Type
 */
class MultipleAdminType extends AbstractType
{
    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return '';
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('admins', CollectionType::class, ['entry_type' => AdminCreateType::class, 'allow_add' => true]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => AdminListModel::class, 'csrf_protection' => false]);
    }
}