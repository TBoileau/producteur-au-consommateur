<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Price;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AddressType
 * @package App\Form
 */
class AddressType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("address", TextType::class, [
                "label" => "Adresse"
            ])
            ->add("restAddress", TextType::class, [
                "label" => "Complément d'adresse",
                "required" => false
            ])
            ->add("zipCode", TextType::class, [
                "label" => "Code postal"
            ])
            ->add("city", TextType::class, [
                "label" => "Ville"
            ])
            ->add("position", PositionType::class, ["label" => false]);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault("data_class", Address::class);
    }
}
