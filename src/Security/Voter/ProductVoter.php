<?php

namespace App\Security\Voter;

use App\Entity\Producer;
use App\Entity\Product;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class ProductVoter
 * @package App\Security\Voter
 */
class ProductVoter extends Voter
{
    public const UPDATE = "update";

    public const DELETE = "delete";

    /**
     * @inheritDoc
     */
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::UPDATE, self::DELETE]) && $subject instanceof Product;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof Producer) {
            return false;
        }

        /** @var Product $subject */

        return $subject->getFarm() === $user->getFarm();
    }
}
