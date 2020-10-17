<?php

namespace App\Handler;

use App\Form\ForgottenPasswordType;
use App\HandlerFactory\AbstractHandler;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ForgottenPasswordHandler
 * @package App\Handler
 */
class ForgottenPasswordHandler extends AbstractHandler
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var FlashBagInterface
     */
    private FlashBagInterface $flashBag;

    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    /**
     * ForgottenPasswordHandler constructor.
     * @param EntityManagerInterface $entityManager
     * @param FlashBagInterface $flashBag
     * @param MailerInterface $mailer
     * @param UserRepository $userRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag,
        MailerInterface $mailer,
        UserRepository $userRepository
    ) {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
    }

    /**
     * @inheritDoc
     */
    protected function process($data, array $options): void
    {
        $user = $this->userRepository->findOneByEmail($data->getEmail());
        $user->hasForgotHisPassword();
        $this->entityManager->flush();
        $email = (new TemplatedEmail())
            ->to(new Address($user->getEmail(), $user->getFullName()))
            ->from("hello@producteurauconsommateur.com")
            ->context(["forgottenPassword" => $user->getForgottenPassword()])
            ->htmlTemplate('emails/forgotten_password.html.twig');
        $this->mailer->send($email);
        $this->flashBag->add(
            "success",
            "Votre demande d'oubli de mot de passe a bien été enregistrée. 
                Vous allez recevoir un email pour réinitialiser votre mot de passe"
        );
    }

    /**
     * @inheritDoc
     */
    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefault("form_type", ForgottenPasswordType::class);
    }
}
