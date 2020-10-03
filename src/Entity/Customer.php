<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Customer
 * @package App\Entity
 * @ORM\Entity
 */
class Customer extends User
{
    public const ROLE = "customer";

    public function getRoles(): array
    {
        return ['ROLE_CUSTOMER'];
    }
}
