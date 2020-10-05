<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * Class Customer
 * @package App\Entity
 * @ORM\Entity
 */
class Customer extends User
{
    public const ROLE = "customer";

    /**
     * @ORM\OneToMany(targetEntity="CartItem", mappedBy="customer", cascade={"persist"}, orphanRemoval=true)
     */
    private Collection $cart;

    /**
     * Customer constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->cart = new ArrayCollection();
    }

    public function getRoles(): array
    {
        return ['ROLE_CUSTOMER'];
    }

    /**
     * @return ArrayCollection|Collection
     */
    public function getCart()
    {
        return $this->cart;
    }

    public function addToCart(Product $product): void
    {
        $products = $this->cart->filter(fn (CartItem $cartItem) => $cartItem->getProduct() == $product);

        if ($products->count() === 0) {
            $cartItem = new CartItem();
            $cartItem->setQuantity(1);
            $cartItem->setCustomer($this);
            $cartItem->setProduct($product);
            $this->cart->add($cartItem);
            return;
        }

        $products->first()->increaseQuantity();
    }

    /**
     * @return float
     */
    public function getTotalCartIncludingTaxes(): float
    {
        return array_sum($this->cart->map(fn (CartItem $cartItem) => $cartItem->getPriceIncludingTaxes())->toArray());
    }
}
