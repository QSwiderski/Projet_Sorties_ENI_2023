<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Location;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('location',
                EntityType::class,
                [
                    "class" => Location::class,
                    "choice_label" => "name",
                    "required"=>false
                ]
            )
            ->add('description')
            ->add('peopleMax')
            ->add('dateStart', DateTimeType::class, ['widget' => 'single_text',
                'label' => 'Début de l\'evenement'])
            ->add('dateFinish', DateTimeType::class, ['widget' => 'single_text',
                'label' => 'Fin de l\'evenement'])
            ->add('dateLimit', DateTimeType::class, ['widget' => 'single_text',
                'label' => 'Limite des inscriptions'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
