<?php

namespace App\Controller;

use App\Form\FarmType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FarmController
 * @package App\Controller
 * @Route("/farm")
 * @IsGranted("ROLE_PRODUCER")
 */
class FarmController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     * @Route("/update", name="farm_update")
     */
    public function update(Request $request): Response
    {
        $form = $this->createForm(FarmType::class, $this->getUser()->getFarm())->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                "success",
                "Les informations de votre exploitation ont été modifiée avec succès."
            );
            return $this->redirectToRoute("farm_update");
        }

        return $this->render("ui/farm/update.html.twig", [
            "form" => $form->createView()
        ]);
    }
}
