<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class CandidatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Firstname',
                'required' => false
            ])
            ->add('surname', TextType::class, [
                'label' => 'Lastname',
                'required' => false
            ])
            ->add('birthday', DateType::class, [
                'label' => 'Birthday',
                'required' => false
            ])
            ->add('phone', TextType::class, [
                'label' => 'Phone number',
                'required' => false
            ])
            ->add('country', TextType::class, [
                'label' => 'Country',
                'required' => false
            ])
            ->add('city', TextType::class, [
                'label' => 'City',
                'required' => false
            ])
            ->add('departement', TextType::class, [
                'label' => 'Department',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
