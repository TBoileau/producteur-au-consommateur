<?php

namespace App\Form;

use App\Entity\Farm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
                "label" => "Nom de votre exploitation",
                "empty_data" => ""
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
                $form = $event->getForm();
                /** @var Farm $farm */
                $farm = $event->getData();

                if ($farm->getId() !== null) {
                    $form
                       ->add("image", ImageType::class, [
                           "label" => false
                       ])
                       ->add("address", AddressType::class, [
                           "label" => false
                       ])
                       ->add("description", TextareaType::class, [
                           "label" => "Présentation de votre exploitation"
                       ]);
                }
            });
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault("data_class", Farm::class);
    }
}
