<?php

namespace App\Form;

use App\Entity\Application;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

class ApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextareaType::class, [
                'label' => 'Message',
                'required' => false,
                'attr' => [
                    'rows' => 5
                ],
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Should be at most 255 characters long',
                    ])
                ]
            ])
            ->add('files', FileType::class, [
                'label' => 'Files',
                'multiple' => true, // avec multiple => true, on à une donc un array de string (fichier)
                'mapped' => false,
                'required' => false,
                'help' => '3 files at most, 2Mb max each',
                'constraints' => [
                    new All([ // constraint All est nécéssaire à cause du multiple => true
                        'constraints' => [
                            new File([
                                'maxSize' => '2M',
                                'maxSizeMessage' => 'File must be at most 2Mb',
                                'mimeTypes' => [
                                    'application/pdf',
                                    'application/x-pdf',
                                ],
                                'mimeTypesMessage' => 'Please upload a valid PDF document',
                            ])
                        ]
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Application::class,
        ]);
    }
}
