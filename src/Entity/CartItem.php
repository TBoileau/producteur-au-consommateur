<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Class CartItem
 * @package App\Entity
 * @ORM\Entity
 */
class CartItem
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private UuidInterface $id;

    /**
     * @ORM\Column(type="integer")
     */
    private int $quantity = 0;

    /**
     * @ORM\ManyToOne(targetEntity="Product")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private Product $product;

    /**
     * @ORM\ManyToOne(targetEntity="Customer", inversedBy="cart")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private ?Customer $customer = null;

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
        if ($this->quantity <= 0) {
            $this->customer->getCart()->removeElement($this);
            $this->customer = null;
        }
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     */
    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    /**
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     */
    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function increaseQuantity(): void
    {
        $this->quantity++;
    }

    /**
     * @return float
     */
    public function getPriceIncludingTaxes(): float
    {
        return $this->product->getPriceIncludingTaxes() * $this->quantity;
    }
}
