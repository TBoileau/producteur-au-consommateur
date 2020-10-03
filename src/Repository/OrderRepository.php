<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class OrderRepository
 * @package App\Repository
 * @method findByOrder(Order $farm): array<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    /**²
     * @inheritDoc
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }
}
