<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Rollerworks\Component\PasswordStrength\Validator\Constraints as RollerworksPassword;

class RegistrationCandidatFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'label' => 'Email',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Email is required',
                    ]),
                    new Email([
                        'message' => 'Please enter a valid email adress'
                    ])
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => [
                    'label' => 'Password',
                    'help' => 'Password must be at least 8 characters long',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Password is required',
                        ]),
                        new Length([
                            'min' => 8,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                        new RollerworksPassword\PasswordRequirements([
                            'requireNumbers' => true,
                            'requireCaseDiff' => true,
                            'requireSpecialCharacter' => true
                        ]),
                    ],
                    'attr' => ['autocomplete' => 'new-password']
                ],
                'second_options' => [
                    'label' => 'Password confirmation'
                ]
            ])
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
