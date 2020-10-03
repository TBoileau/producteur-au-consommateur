<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Producer
 * @package App\Entity
 * @ORM\Entity
 * @ORM\EntityListeners({"App\EntityListener\ProducerListener"})
 */
class Producer extends User
{
    public const ROLE = "producer";

    /**
     * @ORM\OneToOne(targetEntity="Farm", cascade={"persist"}, inversedBy="producer")
     */
    private Farm $farm;

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
