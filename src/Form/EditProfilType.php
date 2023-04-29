<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

class EditProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Pseudo'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Votre pseudo ne peut pas être vide.']),
                    new Length([
                        'max' => 20,
                        'maxMessage' => 'Votre pseudo ne doit pas contenir plus de {{ limit }} caractères.',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9_]+$/',
                        'message' => 'Votre pseudo ne doit pas contenir de caractères spéciaux.',
                    ])
                ],
            ])
            ->add('bio', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control textarea-edit-profil',
                    'placeholder' => 'Bio'
                ],
                'constraints' => [
                    new Length([
                        'max' => 200,
                        'maxMessage' => 'Votre bio ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('localisation', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Localisation',
                ],
                'constraints' => [
                    new Length([
                        'max' => 29,
                        'maxMessage' => 'Votre localisation ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('image', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Votre photo de profil doit être au format JPG ou PNG',
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => 'btn btn-dark',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
