<?php

namespace App\HandlerFactory;

use App\HandlerFactory\HandlerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AbstractHandler
 * @package App\HandlerFactory
 */
abstract class AbstractHandler implements HandlerInterface
{
    /**
     * @var FormFactoryInterface
     */
    private FormFactoryInterface $formFactory;

    /**
     * @var FormInterface
     */
    private FormInterface $form;

    /**
     * @param FormFactoryInterface $formFactory
     * @required
     */
    public function setFormFactory(FormFactoryInterface $formFactory): void
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param mixed|null $data
     * @param array $options
     */
    abstract protected function process($data, array $options): void;

    /**
     * @param OptionsResolver $resolver
     */
    protected function configure(OptionsResolver $resolver): void
    {
    }

    /**
     * @inheritDoc
     */
    public function handle(Request $request, $data = null, array $options = []): bool
    {
        $resolver = new OptionsResolver();

        $resolver->setRequired("form_type");
        $resolver->setDefault("form_options", []);

        $this->configure($resolver);

        $options = $resolver->resolve($options);

        $this->form = $this->formFactory->create(
            $options["form_type"],
            $data,
            $options["form_options"]
        )->handleRequest($request);

        if ($this->form->isSubmitted() && $this->form->isValid()) {
            $this->process($data, $options);
            return true;
        }

        return false;
    }

    public function createView(): FormView
    {
        return $this->form->createView();
    }
}
