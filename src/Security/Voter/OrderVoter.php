<?php

namespace App\Security\Voter;

use App\Entity\Customer;
use App\Entity\Producer;
use App\Entity\Order;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * Class OrderVoter
 * @package App\Security\Voter
 */
class OrderVoter extends Voter
{
    public const CANCEL = "cancel";
    public const REFUSE = "refuse";
    public const SETTLE = "settle";
    public const ACCEPT = "accept";

    /**
     * @var WorkflowInterface
     */
    private WorkflowInterface $orderStateMachine;

    /**
     * OrderVoter constructor.
     * @param WorkflowInterface $orderStateMachine
     */
    public function __construct(WorkflowInterface $orderStateMachine)
    {
        $this->orderStateMachine = $orderStateMachine;
    }

    /**
     * @inheritDoc
     */
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::CANCEL, self::REFUSE, self::SETTLE, self::ACCEPT])
            && $subject instanceof Order;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        /** @var Order $subject */
        switch ($attribute) {
            case self::CANCEL:
                return $user instanceof Customer
                    && $user === $subject->getCustomer()
                    && $this->orderStateMachine->can($subject, "cancel");
            case self::REFUSE:
                return $user instanceof Producer
                    && $user->getFarm() === $subject->getFarm()
                    && $this->orderStateMachine->can($subject, "refuse");
            case self::ACCEPT:
                return $user instanceof Producer
                    && $user->getFarm() === $subject->getFarm()
                    && $this->orderStateMachine->can($subject, "accept");
            case self::SETTLE:
                return $user instanceof Producer
                    && $user->getFarm() === $subject->getFarm()
                    && $this->orderStateMachine->can($subject, "settle");
        }

        throw new \LogicException("Vous n'êtes pas censé arriver ici.");
    }
}
