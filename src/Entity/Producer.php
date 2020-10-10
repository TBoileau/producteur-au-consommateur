<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Producer
 * @package App\Entity
 * @ORM\Entity
 */
class Producer extends User
{
    public const ROLE = "producer";

    /**
     * @ORM\OneToOne(targetEntity="Farm", cascade={"persist"}, inversedBy="producer")
     * @Assert\Valid
     */
    private Farm $farm;

    public function __construct()
    {
        parent::__construct();
        $this->farm = new Farm();
        $this->farm->setProducer($this);
    }

    /**
     * @return array|string[]
     */
    public function getRoles(): array
    {
        return ['ROLE_PRODUCER'];
    }

    /**
     * @return Farm
     */
    public function getFarm(): Farm
    {
        return $this->farm;
    }

    /**
     * @param Farm $farm
     */
    public function setFarm(Farm $farm): void
    {
        $this->farm = $farm;
    }
}
