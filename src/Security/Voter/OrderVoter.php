<?php

namespace App\Security\Voter;

use App\Entity\Customer;
use App\Entity\Producer;
use App\Entity\Order;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class OrderVoter
 * @package App\Security\Voter
 */
class OrderVoter extends Voter
{
    public const CANCEL = "cancel";

    /**
     * @inheritDoc
     */
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::CANCEL]) && $subject instanceof Order;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof Customer) {
            return false;
        }

        /** @var Order $subject */

        return $subject->getState() === "created";
    }
}
