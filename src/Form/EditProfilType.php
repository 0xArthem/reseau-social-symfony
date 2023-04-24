<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EditProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('bio', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control textarea-edit-profil',
                ],
            ])
            ->add('localisation', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Localisation',
                ],
            ])
            ->add('isPublic', null, [
                'label' => 'Profil public',
                'label_attr' => [
                    'class' => 'me-2'
                ]
            ])
            ->add('image', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
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
