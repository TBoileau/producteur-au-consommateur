<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * Class ForgottenPassword
 * @package App\Entity
 * @ORM\Embeddable
 */
class ForgottenPassword
{
    /**
     * @ORM\Column(type="uuid", unique=true, nullable=true)
     */
    private ?string $token = null;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?DateTimeImmutable $requestedAt = null;

    /**
     * ForgottenPassword constructor.
     */
    public function __construct()
    {
        $this->token = Uuid::v4();
        $this->requestedAt = new DateTimeImmutable();
    }

    /**
     * @return string|\Symfony\Component\Uid\UuidV4|null
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string|\Symfony\Component\Uid\UuidV4|null $token
     */
    public function setToken($token): void
    {
        $this->token = $token;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getRequestedAt(): ?DateTimeImmutable
    {
        return $this->requestedAt;
    }

    /**
     * @param DateTimeImmutable|null $requestedAt
     */
    public function setRequestedAt(?DateTimeImmutable $requestedAt): void
    {
        $this->requestedAt = $requestedAt;
    }
}
