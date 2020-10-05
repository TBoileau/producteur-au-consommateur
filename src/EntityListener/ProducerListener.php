<?php

namespace App\EntityListener;

use App\Entity\Farm;
use App\Entity\Producer;
use Symfony\Component\Uid\Uuid;

/**
 * Class ProducerListener
 * @package App\EntityListener
 */
class ProducerListener
{
    /**
     * @param Producer $producer
     */
    public function prePersist(Producer $producer): void
    {
        $farm = new Farm();
        $farm->setProducer($producer);
        $producer->setFarm($farm);
    }
}
