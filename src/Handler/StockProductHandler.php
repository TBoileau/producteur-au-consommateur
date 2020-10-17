<?php

namespace App\Handler;

use App\Form\StockType;
use App\HandlerFactory\AbstractHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class StockProductHandler
 * @package App\Handler
 */
class StockProductHandler extends AbstractHandler
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManage;

    /**
     * @var FlashBagInterface
     */
    private FlashBagInterface $flashBag;

    /**
     * StockProductHandler constructor.
     * @param EntityManagerInterface $entityManage
     * @param FlashBagInterface $flashBag
     */
    public function __construct(EntityManagerInterface $entityManage, FlashBagInterface $flashBag)
    {
        $this->entityManage = $entityManage;
        $this->flashBag = $flashBag;
    }

    /**
     * @inheritDoc
     */
    protected function process($data, array $options): void
    {
        $this->entityManage->flush();
        $this->flashBag->add(
            "success",
            "le stock de votre produit ont été modifié avec succès."
        );
    }

    /**
     * @inheritDoc
     */
    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefault("form_type", StockType::class);
    }
}
