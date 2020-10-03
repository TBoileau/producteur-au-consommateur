<?php

namespace App\EntityListener;

use App\Entity\Product;
use Symfony\Component\Security\Core\Security;

/**
 * Class ProductListener
 * @package App\EntityListener
 */
class ProductListener
{
    /**
     * @var Security
     */
    private Security $security;

    /**
     * ProductListener constructor.
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @param Product $product
     */
    public function prePersist(Product $product): void
    {
        if ($product->getFarm() !== null) {
            return;
        }

        $product->setFarm($this->security->getUser()->getFarm());
    }
}
