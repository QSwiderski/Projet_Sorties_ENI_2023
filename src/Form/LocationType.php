<?php

namespace App\Form;

use App\Entity\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('adress', TextType::class, [
                'label' => 'n°, rue'
            ])
            ->add('postcode', TextType::class, [
                'label' => 'Code Postal',
                'constraints' => [
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Un code postal doit avoir {{ limit }} chiffres',
                        'max' => 5,
                        'maxMessage' => 'Un code postal doit avoir {{ limit }} chiffres'
                    ])
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville'
            ])
            ->add('latitude')
            ->add('longitude');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }
}
