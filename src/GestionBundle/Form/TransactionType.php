<?php

namespace GestionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TransactionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('title')
        ->add('amount')
        ->add('isInput')
        ->add('description')
        ->add('isValid')
        //->add('createdAt')
        //->add('updatedAt')
        ->add('category', EntityType::class, array(
            'class' => 'GestionBundle:Category',
            'choice_label' => 'title',
        ))
        ->add('tag', EntityType::class, array(
            'class' => 'GestionBundle:Tag',
            'choice_label' => 'name',
            'multiple' => true,

        ))
        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'GestionBundle\Entity\Transaction'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'gestionbundle_transaction';
    }


}
