<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Job;
use App\Entity\Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

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
                        'minMessage' => 'Should be at least 3 characters long',
                        'max' => 255,
                        'maxMessage' => 'Should be at most 255 characters long',
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
                        'minMessage' => 'Should be at least 3 characters long',
                        'max' => 255,
                        'maxMessage' => 'Should be at most 255 characters long',
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
            ->add('category', EntityType::class, [
                'label' => 'Job field*',
                'class' => Category::class,
                'choice_label' => 'name',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Job field* is required'
                    ]),
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description*',
                'required' => true,
                'attr' => [
                    'rows' => 10
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Description is required'
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Should be at least 3 characters long',
                        'max' => 1000,
                        'maxMessage' => 'Should be at most 1000 characters long',
                    ])
                ]
            ])
            ->add('pay', TextType::class, [
                'label' => 'Incomes',
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Should be at most 255 characters long',
                    ])
                ]
            ])
            ->add('isRemote', CheckboxType::class, [
                'label' => 'Remote work',
                'required' => false,
            ])
            ->add('location', TextType::class, [
                'label' => 'Location',
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Should be at most 255 characters long',
                    ])
                ]
            ])
            ->add('departement', TextType::class, [
                'label' => 'Department',
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 5,
                        'maxMessage' => 'Should be at most 5 characters long',
                    ])
                ]
            ])
            ->add('experienceNeeded', IntegerType::class, [
                'label' => 'Experience needed',
                'required' => false,
                'constraints' => [
                    new Positive([
                        'message' => 'Experience needed must be a positive number'
                    ])
                ]
            ])
            // https://symfony.com/doc/current/form/events.html#event-listeners
            // https://symfony.com/doc/current/form/dynamic_form_modification.html#dynamic-generation-for-submitted-forms
            ->addEventListener(FormEvents::POST_SUBMIT, [$this, 'onPostSubmit']);
    }

    public function onPostSubmit(FormEvent $event): void
    {
        $job = $event->getData();

        if ($job->getIsRemote() === true) {
            $job->setLocation(null);
            $job->setDepartement(null);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Job::class,
        ]);
    }
}
