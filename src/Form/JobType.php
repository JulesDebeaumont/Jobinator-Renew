<?php

namespace App\Form;

use App\Entity\Job;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('pay')
            ->add('location')
            ->add('isRemote')
            ->add('experienceNeeded')
            ->add('isSignaled')
            ->add('departement')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('category')
            ->add('type')
            ->add('recruter')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Job::class,
        ]);
    }
}
