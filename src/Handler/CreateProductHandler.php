<?php

namespace App\Handler;

use App\Form\ProductType;
use App\HandlerFactory\AbstractHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CreateProductHandler
 * @package App\Handler
 */
class CreateProductHandler extends AbstractHandler
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
     * CreateProductHandler constructor.
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
        $this->entityManage->persist($data);
        $this->entityManage->flush();
        $this->flashBag->add(
            "success",
            "Votre produit ont été créé avec succès."
        );
    }

    /**
     * @inheritDoc
     */
    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefault("form_type", ProductType::class);
    }
}
