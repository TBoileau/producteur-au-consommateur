<?php

namespace App\Controller;

use App\Entity\Farm;
use App\Form\FarmType;
use App\Repository\FarmRepository;
use App\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class FarmController
 * @package App\Controller
 * @Route("/farm")
 */
class FarmController extends AbstractController
{
    /**
     * @param FarmRepository $farmRepository
     * @return JsonResponse
     * @Route("/all", name="farm_all")
     */
    public function all(FarmRepository $farmRepository, SerializerInterface $serializer): JsonResponse
    {
        $farms = $serializer->serialize($farmRepository->findAll(), 'json', ["groups" => "read"]);
        return new JsonResponse($farms, JsonResponse::HTTP_OK, [], true);
    }

    /**
     * @param Farm $farm
     * @param ProductRepository $productRepository
     * @return Response
     * @Route("/{slug}/show", name="farm_show")
     */
    public function show(Farm $farm, ProductRepository $productRepository): Response
    {
        return $this->render("ui/farm/show.html.twig", [
            "farm" => $farm,
            "products" => $productRepository->findByFarm($farm)
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/update", name="farm_update")
     * @IsGranted("ROLE_PRODUCER")
     */
    public function update(Request $request): Response
    {
        $form = $this->createForm(FarmType::class, $this->getUser()->getFarm(), [
            "validation_groups" => ["Default", "edit"]
        ])->handleRequest($request);

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
