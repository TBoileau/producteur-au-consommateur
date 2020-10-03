<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class UserRepository
 * @package App\Repository
 * @method findOneByEmail(string $email): ?User
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * @inheritDoc
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }
}
