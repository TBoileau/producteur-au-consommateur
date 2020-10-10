<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class Farm
 * @package App\Entity
 * @ORM\Entity
 * @ORM\EntityListeners({"App\EntityListener\FarmListener"})
 */
class Farm
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     * @Groups({"read"})
     */
    private ?UuidInterface $id = null;

    /**
     * @ORM\Column()
     * @Assert\NotBlank
     * @Groups({"read"})
     */
    private string $name = "";

    /**
     * @ORM\Column(unique=true)
     * @Groups({"read"})
     */
    private string $slug;

    /**
     * @ORM\Column(nullable=true, type="text")
     * @Assert\NotBlank(groups={"edit"})
     */
    private ?string $description = null;

    /**
     * @ORM\OneToOne(targetEntity="Producer", mappedBy="farm")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private Producer $producer;

    /**
     * @ORM\Embedded(class="Address")
     * @Assert\Valid(groups={"edit"})
     * @Groups({"read"})
     */
    private ?Address $address = null;

    /**
     * @ORM\Embedded(class="Image")
     * @Assert\Valid(groups={"edit"})
     */
    private Image $image;

    /**
     * @return UuidInterface|null
     */
    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return Producer
     */
    public function getProducer(): Producer
    {
        return $this->producer;
    }

    /**
     * @param Producer $producer
     */
    public function setProducer(Producer $producer): void
    {
        $this->producer = $producer;
    }

    /**
     * @return Address|null
     */
    public function getAddress(): ?Address
    {
        return $this->address;
    }

    /**
     * @param Address|null $address
     */
    public function setAddress(?Address $address): void
    {
        $this->address = $address;
    }

    /**
     * @return Image
     */
    public function getImage(): Image
    {
        return $this->image;
    }

    /**
     * @param Image $image
     */
    public function setImage(Image $image): void
    {
        $this->image = $image;
    }
}
