<?php

namespace App\Form;

use App\Entity\Job;
use App\Entity\Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class JobType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name*',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Name is required'
                    ]),
                    new Length([
                        'min' => 3,
                        'max' => 255
                    ])
                ]
            ])
            ->add('company', TextType::class, [
                'label' => 'Company name*',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Contract type is required'
                    ]),
                    new Length([
                        'min' => 3,
                        'max' => 255
                    ])
                ]
            ])
            ->add('type', EntityType::class, [
                'label' => 'Contract type*',
                'class' => Type::class,
                'choice_label' => 'name',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Contract type is required'
                    ]),
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description*',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Description is required'
                    ]),
                    new Length([
                        'min' => 3,
                        'max' => 1000
                    ])
                ]
            ])
            ->add('pay', TextType::class, [
                'label' => 'Incomes',
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 255
                    ])
                ]
            ])
            ->add('location', TextType::class, [
                'label' => 'Location',
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 255
                    ])
                ]
            ])
            ->add('departement', TextType::class, [
                'label' => 'Department',
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 5
                    ])
                ]
            ])
            ->add('isRemote', CheckboxType::class, [
                'label' => 'Remote work'
            ])
            ->add('experienceNeeded', IntegerType::class, [
                'label' => 'Experience needed',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Job::class,
        ]);
    }
}
