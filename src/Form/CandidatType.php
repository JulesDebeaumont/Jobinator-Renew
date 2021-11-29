<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;

class CandidatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Firstname',
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Name should be at most 255 characters long',
                    ])
                ]
            ])
            ->add('surname', TextType::class, [
                'label' => 'Lastname',
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Surname should be at most 255 characters long',
                    ])
                ]
            ])
            ->add('birthday', BirthdayType::class, [
                'label' => 'Birthday',
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('phone', TelType::class, [
                'label' => 'Phone number',
                'required' => false
            ])
            ->add('country', TextType::class, [
                'label' => 'Country',
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Country should be at most 255 characters long',
                    ])
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'City',
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'City should be at most 255 characters long',
                    ])
                ]
            ])
            ->add('departement', TextType::class, [
                'label' => 'Department',
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Department should be at most 255 characters long',
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
