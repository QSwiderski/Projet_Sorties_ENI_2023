<?php

namespace App\Form;

use App\Entity\School;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QueryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                "label" => "Le nom contient",
                "required" => false
            ])
            ->add('school', EntityType::class, [
                "class" => School::class,
                "choice_label" => 'name',
                "required" => false
            ])
            ->add('dateMin', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'A partir de',
                "required" => false
            ])
            ->add('dateMax', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Pas après',
                "required" => false
            ])
            ->add('organizer', CheckboxType::class, [
                'label' => 'Mes evenements',
                "required" => false
            ])
            ->setMethod('GET');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
