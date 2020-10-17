<?php

namespace App\Controller;

use App\Form\UserInfoType;
use App\Form\UserPasswordType;
use App\Handler\CartHandler;
use App\Handler\UserInfoHandler;
use App\Handler\UserPasswordHandler;
use App\HandlerFactory\HandlerFactoryInterface;
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
     * @param HandlerFactoryInterface $handlerFactory
     * @return Response
     * @Route("/edit-info", name="user_edit_info")
     */
    public function editInfo(Request $request, HandlerFactoryInterface $handlerFactory): Response
    {
        $handler = $handlerFactory->createHandler(UserInfoHandler::class);

        if ($handler->handle($request, $this->getUser())) {
            return $this->redirectToRoute("user_edit_info");
        }

        return $this->render("ui/user/edit_info.html.twig", [
            "form" => $handler->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param HandlerFactoryInterface $handlerFactory
     * @return Response
     * @Route("/edit-password", name="user_edit_password")
     */
    public function editPassword(Request $request, HandlerFactoryInterface $handlerFactory): Response
    {
        $handler = $handlerFactory->createHandler(UserPasswordHandler::class);

        if ($handler->handle($request, $this->getUser())) {
            return $this->redirectToRoute("user_edit_password");
        }

        return $this->render("ui/user/edit_password.html.twig", [
            "form" => $handler->createView()
        ]);
    }
}
