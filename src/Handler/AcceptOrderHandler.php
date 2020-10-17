<?php

namespace App\Handler;

use App\Form\AcceptOrderType;
use App\HandlerFactory\AbstractHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * Class AcceptOrderHandler
 * @package App\Handler
 */
class AcceptOrderHandler extends AbstractHandler
{
    /**
     * @var FlashBagInterface
     */
    private FlashBagInterface $flashBag;

    /**
     * @var WorkflowInterface
     */
    private WorkflowInterface $orderStateMachine;

    /**
     * AcceptOrderHandler constructor.
     * @param FlashBagInterface $flashBag
     * @param WorkflowInterface $orderStateMachine
     */
    public function __construct(
        FlashBagInterface $flashBag,
        WorkflowInterface $orderStateMachine
    ) {
        $this->flashBag = $flashBag;
        $this->orderStateMachine = $orderStateMachine;
    }

    /**
     * @inheritDoc
     */
    protected function process($data, array $options): void
    {
        $this->orderStateMachine->apply($data, 'accept');
        $this->flashBag->add('success', "La commande a été acceptée avec succès.");
    }

    /**
     * @inheritDoc
     */
    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefault("form_type", AcceptOrderType::class);
    }
}
