<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\ForgottenPasswordInput;
use App\Entity\Customer;
use App\Entity\Producer;
use App\Entity\User;
use App\Form\ForgottenPasswordType;
use App\Form\RegistrationType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Uid\Uuid;

/**
 * Class SecurityController
 * @package App\Controller
 */
class SecurityController extends AbstractController
{
    /**
     * @param string $role
     * @param Request $request
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @return Response
     * @Route("/registration/{role}", name="security_registration")
     */
    public function registration(
        string $role,
        Request $request,
        UserPasswordEncoderInterface $userPasswordEncoder
    ): Response {
        $user = Producer::ROLE === $role ? new Producer() : new Customer();
        $user->setId(Uuid::v4());

        $form = $this->createForm(RegistrationType::class, $user, [
            "validation_groups" => ["Default", "password"]
        ])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordEncoder->encodePassword($user, $user->getPlainPassword())
            );
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash("success", "Votre inscription a été effectuée avec succès.");
            return $this->redirectToRoute("index");
        }

        return $this->render("ui/security/registration.html.twig", [
            "form" => $form->createView()
        ]);
    }

    /**
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render("ui/security/login.html.twig", [
            "last_username" => $authenticationUtils->getLastUsername(),
            "error" => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout(): void
    {
    }

    /**
     * @param Request $request
     * @param UserRepository $userRepository
     * @param MailerInterface $mailer
     * @return Response
     * @Route("/forgotten-password", name="security_forgotten_password")
     */
    public function forgottenPassword(
        Request $request,
        UserRepository $userRepository,
        MailerInterface $mailer
    ): Response {
        $forgottenPasswordInput = new ForgottenPasswordInput();

        $form = $this->createForm(ForgottenPasswordType::class, $forgottenPasswordInput)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $userRepository->findOneByEmail($forgottenPasswordInput->getEmail());
            $user->hasForgotHisPassword();
            $this->getDoctrine()->getManager()->flush();
            $email = (new TemplatedEmail())
                ->to(new Address($user->getEmail(), $user->getFullName()))
                ->from("hello@producteurauconsommateur.com")
                ->context(["forgottenPassword" => $user->getForgottenPassword()])
                ->htmlTemplate('emails/forgotten_password.html.twig');
            $mailer->send($email);
            $this->addFlash(
                "success",
                "Votre demande d'oubli de mot de passe a bien été enregistrée. 
                Vous allez recevoir un email pour réinitialiser votre mot de passe"
            );
            return $this->redirectToRoute("security_login");
        }

        return $this->render("ui/security/forgotten_password.html.twig", [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/reset-password/{token}", name="security_reset_password")
     * @param string $token
     * @param Request $request
     * @param UserRepository $userRepository
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function resetPassword(
        string $token,
        Request $request,
        UserRepository $userRepository,
        UserPasswordEncoderInterface $userPasswordEncoder
    ): Response {
        $user = $userRepository->getUserByForgottenPasswordToken(Uuid::fromString($token));

        if (null === $user) {
            $this->addFlash("danger", "Cette demande d'oubli de mot de passe n'existe pas.");
        }

        $form = $this->createForm(ResetPasswordType::class, $user, [
            "validation_groups" => ["password"]
        ])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordEncoder->encodePassword($user, $user->getPlainPassword())
            );
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                "success",
                "Votre mot de passe a été modifié avec succès."
            );
            return $this->redirectToRoute("security_login");
        }

        return $this->render("ui/security/reset_password.html.twig", [
            "form" => $form->createView()
        ]);
    }
}
