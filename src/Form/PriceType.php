<?php

namespace App\Form;

use App\Entity\Price;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PriceType
 * @package App\Form
 */
class PriceType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("unitPrice", MoneyType::class, [
                "label" => "Prix unitaire HT",
                "scale" => 0,
                "empty_data" => 0
            ])
            ->add("vat", ChoiceType::class, [
                "label" => "TVA",
                "choices" => [
                    "2,1%" => 2.1,
                    "5,5%" => 5.5,
                    "10%" => 10,
                    "20%" => 20
                ]
            ]);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault("data_class", Price::class);
    }
}
