<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Rollerworks\Component\PasswordStrength\Validator\Constraints as RollerworksPassword;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Psr\Container\ContainerInterface;

class RegistrationRecruterFormType extends AbstractType
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

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
            ->add('company', TextType::class, [
                'label' => 'Company',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Company is required',
                    ]),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Should be at most 255 characters long',
                    ])
                ]
            ])
            ->add('captcha', CaptchaType::class, [
                'disabled' => $this->container->getParameter('captchas_disabled')
                // 'reload' => true,
                // 'as_url' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
