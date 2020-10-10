<?php

namespace App\Controller;

use App\Form\UserInfoType;
use App\Form\UserPasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserController
 * @package App\Controller
 * @IsGranted("ROLE_USER")
 */
class UserController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     * @Route("/edit-info", name="user_edit_info")
     */
    public function editInfo(Request $request): Response
    {
        $form = $this->createForm(UserInfoType::class, $this->getUser())->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash("success", "Vos informations personnelles a été modifiées avec succès.");
            return $this->redirectToRoute("user_edit_info");
        }

        return $this->render("ui/user/edit_info.html.twig", [
            "form" => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @return Response
     * @Route("/edit-password", name="user_edit_password")
     */
    public function editPassword(
        Request $request,
        UserPasswordEncoderInterface $userPasswordEncoder
    ): Response {
        $form = $this->createForm(UserPasswordType::class, $this->getUser())->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getUser()->setPassword(
                $userPasswordEncoder->encodePassword($this->getUser(), $this->getUser()->getPlainPassword())
            );
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash("success", "Votre mot de passe a été modifié avec succès.");
            return $this->redirectToRoute("user_edit_password");
        }

        return $this->render("ui/user/edit_password.html.twig", [
            "form" => $form->createView()
        ]);
    }
}
