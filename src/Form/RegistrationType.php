<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RegistrationType
 * @package App\Form
 */
class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("email", EmailType::class, [
                "label" => "Adresse email",
                "empty_data" => ""
            ])
            ->add("plainPassword", PasswordType::class, [
                "label" => "Mot de passe",
                "empty_data" => ""
            ])
            ->add("firstName", TextType::class, [
                "label" => "Prénom",
                "empty_data" => ""
            ])
            ->add("lastName", TextType::class, [
                "label" => "Nom",
                "empty_data" => ""
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault("data_class", User::class);
    }
}
