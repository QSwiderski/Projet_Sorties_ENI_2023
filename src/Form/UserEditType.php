<?php

namespace App\Form;

use App\Entity\School;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudonyme'
            ])
            ->add('name', TextType::class, [
                'label' => 'Prenom'
            ])
            ->add('surname', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('phone', TextType::class, [
                'label' => 'N° de Telephone'
            ])
            ->add('school', EntityType::class, [
                "class" => School::class,
                "choice_label" => 'name']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
