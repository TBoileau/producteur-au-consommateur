<?php

namespace App\Form;

use App\Entity\Farm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FarmType
 * @package App\Form
 */
class FarmType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("name", TextType::class, [
                "label" => "Nom de votre exploitation"
            ])
            ->add("image", ImageType::class, [
                "label" => false
            ])
            ->add("address", AddressType::class, ["label" => false])
            ->add("description", TextareaType::class, [
                "label" => "Présentation de votre exploitation"
            ]);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault("data_class", Farm::class);
    }
}
