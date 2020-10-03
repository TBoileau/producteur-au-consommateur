<?php

namespace App\Dto;

use App\Validator\EmailExists;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ForgottenPasswordInput
 * @package App\Dto
 */
class ForgottenPasswordInput
{
    /**
     * @Assert\NotBlank
     * @Assert\Email
     * @EmailExists
     */
    private string $email = "";

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}
