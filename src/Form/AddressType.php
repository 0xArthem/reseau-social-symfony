<?php
namespace App\Form;

use App\Entity\Address;
use Doctrine\DBAL\Types\StringType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Intl\Countries;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('fullName', TextType::class, [
            'label' => false,
            'attr' => [
                'placeholder' => 'Nom de l\'adresse'
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez saisir le nom de l\'adresse'
                ]),
                new Length([
                    'min' => 3,
                    'max' => 255,
                    'minMessage' => 'Le nom de l\'adresse doit contenir au moins {{ limit }} caractères',
                    'maxMessage' => 'Le nom de l\'adresse ne doit pas dépasser {{ limit }} caractères'
                ])
            ]
        ])
        ->add('company', TextType::class, [
            'label' => false,
            'required' => false,
            'attr' => [
                'placeholder' => 'Entreprise'
            ]
        ])
        ->add('address', TextareaType::class, [
            'label' => false,
            'attr' => [
                'placeholder' => 'Adresse'
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez saisir l\'adresse'
                ]),
                new Length([
                    'min' => 3,
                    'max' => 255,
                    'minMessage' => 'L\'adresse doit contenir au moins {{ limit }} caractères',
                    'maxMessage' => 'L\'adresse ne doit pas dépasser {{ limit }} caractères'
                ])
            ]
        ])
        ->add('complement', TextareaType::class, [
            'label' => false,
            'required' => false,
            'attr' => [
                'placeholder' => 'Informations supplémentaires'
            ]
        ])
        ->add('phone', TextType::class, [
            'label' => false,
            'attr' => [
                'placeholder' => 'Numéro de téléphone'
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez saisir le numéro de téléphone'
                ]),
                new Length([
                    'min' => 8,
                    'max' => 20,
                    'minMessage' => 'Le numéro de téléphone doit contenir au moins {{ limit }} caractères',
                    'maxMessage' => 'Le numéro de téléphone ne doit pas dépasser {{ limit }} caractères'
                ])
            ]
        ])
        ->add('city', TextType::class, [
            'label' => false,
            'attr' => [
                'placeholder' => 'Ville'
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer une ville.',
                ]),
                new Length([
                    'min' => 2,
                    'max' => 255,
                    'minMessage' => 'La ville doit contenir au moins {{ limit }} caractères.',
                    'maxMessage' => 'La ville ne doit pas dépasser {{ limit }} caractères.',
                ]),
            ],
        ])
        ->add('codePostal', IntegerType::class, [
            'label' => false,
            'attr' => [
                'placeholder' => 'Code postal'
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer un code postal.',
                ]),
                new Length([
                    'min' => 5,
                    'max' => 5,
                    'exactMessage' => 'Le code postal doit contenir exactement {{ limit }} chiffres.',
                ]),
                new Regex([
                    'pattern' => '/^[0-9]*$/',
                    'message' => 'Le code postal doit contenir uniquement des chiffres'
                ])
            ],
        ])
        ->add('country', CountryType::class, [
            'label' => false,
            'attr' => [
                'placeholder' => 'Pays'
            ],
            'preferred_choices' => [
                'FR', // pays en préférence
            ],
            'placeholder' => 'Sélectionner un pays',
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez sélectionner un pays.',
                ]),
            ],
            'choices' => array_flip(array_map('ucfirst', Countries::getNames())),
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Enregistrer'
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
