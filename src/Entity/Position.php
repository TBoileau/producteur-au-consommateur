<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class Position
 * @package App\Entity
 * @ORM\Embeddable
 */
class Position
{
    /**
     * @ORM\Column(type="decimal", precision=16, scale=13, nullable=true)
     * @Assert\NotBlank
     * @Groups({"read"})
     */
    private ?float $latitude = null;

    /**
     * @ORM\Column(type="decimal", precision=16, scale=13, nullable=true)
     * @Assert\NotBlank
     * @Groups({"read"})
     */
    private ?float $longitude = null;

    /**
     * @return float|null
     */
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    /**
     * @param float|null $latitude
     */
    public function setLatitude(?float $latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @return float|null
     */
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    /**
     * @param float|null $longitude
     */
    public function setLongitude(?float $longitude): void
    {
        $this->longitude = $longitude;
    }
}
