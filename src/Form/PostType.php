<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\PostTag;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Titre'
                ],
                'constraints' => [
                    new Length([
                        'max' => 200,
                        'maxMessage' => 'Le titre ne peut pas dépasser {{ limit }} caractères.',
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
                        'mimeTypesMessage' => 'Veuillez sélectionner une image au format JPG ou PNG',
                    ]),
                ],
            ])
            ->add('content', CKEditorType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control textarea-edit-profil',
                    'placeholder' => 'Texte'
                ],
            ])
            ->add('link', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Lien 1'
                ],
                'constraints' => [
                    new Url([
                        'message' => 'Veuillez saisir une URL valide',
                    ]),
                ],
            ])
            ->add('linkName', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Intitulé du lien 1'
                ]
            ])
            ->add('link2', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Lien 2'
                ],
                'constraints' => [
                    new Url([
                        'message' => 'Veuillez saisir une URL valide',
                    ]),
                ],
            ])
            ->add('linkName2', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Intitulé du lien 2'
                ]
            ])
            ->add('link3', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Lien 3'
                ],
                'constraints' => [
                    new Url([
                        'message' => 'Veuillez saisir une URL valide',
                    ]),
                ],
            ])
            ->add('linkName3', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Intitulé du lien 3'
                ]
            ])
            ->add('link4', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Lien 4'
                ],
                'constraints' => [
                    new Url([
                        'message' => 'Veuillez saisir une URL valide',
                    ]),
                ],
            ])
            ->add('linkName4', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Intitulé du lien 4'
                ]
            ])
            ->add('link5', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Lien 5'
                ],
                'constraints' => [
                    new Url([
                        'message' => 'Veuillez saisir une URL valide',
                    ]),
                ],
            ])
            ->add('linkName5', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Intitulé du lien 5'
                ]
            ])
            ->add('link6', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Lien 6'
                ],
                'constraints' => [
                    new Url([
                        'message' => 'Veuillez saisir une URL valide',
                    ]),
                ],
            ])
            ->add('linkName6', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Intitulé du lien 6'
                ]
            ])
            ->add('posttag', EntityType::class, [
                'class' => PostTag::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('isPinned', null, [
                'label' => 'Voulez-vous épingler cette publication ?',
                'label_attr' => [
                    'class' => 'me-2',
                ],
                'attr' => ['class' => 'form-check-input'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Sauvegarder',
                'attr' => [
                    'class' => 'btn btn-dark'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
