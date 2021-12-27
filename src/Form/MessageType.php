<?php

namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'title',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Title is required'
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Should be at least 3 characters long',
                        'max' => 255,
                        'maxMessage' => 'Should be at most 255 characters long',
                    ])
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'message',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'The content is required'
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Should be at least 3 characters long',
                        'max' => 1000,
                        'maxMessage' => 'Should be at most 255 characters long',
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
