<?php

namespace App\Form;

use App\Entity\ReviewsProduct;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ReviewsProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
    ->add('note', IntegerType::class, [
        'label' => false,
        'attr' => ['max' => 5, 'placeholder' => 'Votre note'],
        'constraints' => [
            new NotBlank([
                'message' => 'Veuillez saisir une note',
            ]),
            new Range([
                'min' => 0,
                'max' => 5,
                'notInRangeMessage' => 'La note doit être comprise entre {{ min }} et {{ max }}.',
            ]),
        ],
    ])
    ->add('comment', TextareaType::class, [
        'label' => false,
        'attr' => [
            'placeholder' => 'Votre commentaire'
        ],
        'constraints' => [
            new NotBlank([
                'message' => 'Veuillez saisir un commentaire',
            ]),
            new Length([
                'min' => 5,
                'maxMessage' => 'Votre commentaire doit contenir au moins {{ min }} caractères.',
            ]),
        ],
    ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReviewsProduct::class,
        ]);
    }
}
