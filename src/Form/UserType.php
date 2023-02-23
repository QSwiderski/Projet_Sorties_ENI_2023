<?php

namespace App\Form;

use App\Entity\School;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'label' => 'Votre E-Mail'
            ])
            ->add('isAdmin', CheckboxType::class, [
                'label' => 'Donner les droits administrateur',
                'mapped' => false,
                'required' => false
            ])
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'type' => PasswordType::class,
                'invalid_message' => 'Le mot de passe doit être identique',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation du mot de passe'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un mot de passe',
                    ]),
                    new Length([
                        'min' => 4,
                        'minMessage' => 'Un mot de passe ne peux avoir plus de {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                        'maxMessage' => 'Un mot de passe ne peux dépasser {{ limit }} caractères'
                    ]),
                ],
            ])
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
