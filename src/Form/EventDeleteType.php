<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use \Symfony\Component\Form\Extension\Core\Type\TextType;

class EventDeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cancel_reason', TextType::class,
                [
                    "required"=>true,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Merci de donner une raison de suppression',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Navré ceci est trop court pour une raison valable',
                            'max' => 4096,
                        ]),
                    ],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
