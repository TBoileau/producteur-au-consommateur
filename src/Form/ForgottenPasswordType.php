<?php

namespace App\Form;

use App\Dto\ForgottenPasswordInput;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ForgottenPasswordType
 * @package App\Form
 */
class ForgottenPasswordType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("email", EmailType::class);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault("data_class", ForgottenPasswordInput::class);
    }
}
