<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Entity\Shows;

class ShowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 1,
                        'max' => 150,
                        'minMessage' => 'Nazwa musi składać się z przynajmniej {{ limit }} znaków',
                        'maxMessage' => 'Nazwa nie może się składać z więcej niz {{ limit }} znaków',
                    ]),
                    new NotBlank()
                ],
            ])
            ->add('database_table_name', TextType::class,  [
                'constraints' => [
                    new Length([
                        'min' => 1,
                        'max' => 150,
                        'minMessage' => 'Nazwa musi składać się z przynajmniej {{ limit }} znaków',
                        'maxMessage' => 'Nazwa nie może się składać z więcej niz {{ limit }} znaków',
                    ]),
                    new NotBlank()
                ],
            ])
            ->add('picture', FileType::class, [
                'constraints' => [
                    new Image([
                        'maxSize' => '4096k', 
                        'mimeTypesMessage' => 'Proszę przesyłać tylko zdjęcia'
                    ]),
                    new NotBlank()
                ],
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Shows::class,
        ]);
    }
}
