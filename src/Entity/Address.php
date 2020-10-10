<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class Address
 * @package App\Entity
 * @ORM\Embeddable
 */
class Address
{
    /**
     * @ORM\Column(nullable=true)
     * @Assert\NotBlank(groups={"edit"})
     * @Groups({"read"})
     */
    private ?string $address = null;

    /**
     * @ORM\Column(nullable=true)
     * @Groups({"read"})
     */
    private ?string $restAddress = null;

    /**
     * @ORM\Column(length=5, nullable=true)
     * @Assert\NotBlank(groups={"edit"})
     * @Assert\Regex(pattern="/^[A-Za-z0-9]{5}$/", message="Code postal invalide.", groups={"edit"})
     * @Groups({"read"})
     */
    private ?string $zipCode = null;

    /**
     * @ORM\Column(nullable=true)
     * @Assert\NotBlank(groups={"edit"})
     * @Groups({"read"})
     */
    private ?string $city = null;

    /**
     * @ORM\Embedded(class="Position")
     * @Assert\Valid(groups={"edit"})
     * @Groups({"read"})
     */
    private ?Position $position = null;

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     */
    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return string|null
     */
    public function getRestAddress(): ?string
    {
        return $this->restAddress;
    }

    /**
     * @param string|null $restAddress
     */
    public function setRestAddress(?string $restAddress): void
    {
        $this->restAddress = $restAddress;
    }

    /**
     * @return string|null
     */
    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    /**
     * @param string|null $zipCode
     */
    public function setZipCode(?string $zipCode): void
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     */
    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return Position|null
     */
    public function getPosition(): ?Position
    {
        return $this->position;
    }

    /**
     * @param Position|null $position
     */
    public function setPosition(?Position $position): void
    {
        $this->position = $position;
    }
}
