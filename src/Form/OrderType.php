<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Transporter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];
        $builder
            ->add('addresses', EntityType::class, [
                'class' => Address::class,
                'label' => false,
                'required' => true,
                'multiple' => false,
                'choices' => $user->getAddresses(),
                // 'expanded' => true,
                'attr' => [
                    'class' => 'form-select',
                ]
            ])
            ->add('transporter', EntityType::class, [
                'class' => Transporter::class,
                'label' => false,
                'required' => true,
                'multiple' => false,
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'user' => []
            // Configure your form options here
        ]);
    }
}
