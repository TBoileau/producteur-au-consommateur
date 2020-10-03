<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Producer;
use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\User;
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
     * @Route("/registration/{role}", name="registration")
     */
    public function registration(
        string $role,
        Request $request,
        UserPasswordEncoderInterface $userPasswordEncoder
    ): Response {
        $user = Producer::ROLE === $role ? new Producer() : new Customer();
        $user->setId(Uuid::v4());

        $form = $this->createForm(RegistrationType::class, $user)->handleRequest($request);

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
}
