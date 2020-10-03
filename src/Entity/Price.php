<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Price
 * @package App\Entity
 * @ORM\Embeddable
 */
class Price
{
    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\GreaterThan(0)
     */
    private int $unitPrice = 0;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     * @Assert\NotBlank
     */
    private float $vat = 0;

    /**
     * @return int
     */
    public function getUnitPrice(): int
    {
        return $this->unitPrice;
    }

    /**
     * @param int $unitPrice
     */
    public function setUnitPrice(int $unitPrice): void
    {
        $this->unitPrice = $unitPrice;
    }

    /**
     * @return float|int
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * @param float|int $vat
     */
    public function setVat($vat): void
    {
        $this->vat = $vat;
    }
}
