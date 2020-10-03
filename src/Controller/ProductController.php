<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\FarmType;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProductController
 * @package App\Controller
 * @Route("/products")
 * @IsGranted("ROLE_PRODUCER")
 */
class ProductController extends AbstractController
{
    /**
     * @param ProductRepository $productRepository
     * @return Response
     * @Route("/", name="product_index")
     */
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render("ui/product/index.html.twig", [
            "products" => $productRepository->findByFarm($this->getUser()->getFarm())
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/create", name="product_create")
     */
    public function create(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                "success",
                "Votre produit ont été créé avec succès."
            );
            return $this->redirectToRoute("product_index");
        }

        return $this->render("ui/product/create.html.twig", [
            "form" => $form->createView()
        ]);
    }

    /**
     * @param Product $product
     * @param Request $request
     * @return Response
     * @Route("/{id}/update", name="product_update")
     * @IsGranted("update", subject="product")
     */
    public function update(Product $product, Request $request): Response
    {
        $form = $this->createForm(ProductType::class, $product)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                "success",
                "Votre produit ont été modifié avec succès."
            );
            return $this->redirectToRoute("product_index");
        }

        return $this->render("ui/product/update.html.twig", [
            "form" => $form->createView()
        ]);
    }

    /**
     * @param Product $product
     * @return Response
     * @Route("/{id}/delete", name="product_delete")
     * @IsGranted("delete", subject="product")
     */
    public function delete(Product $product): Response
    {
        $this->getDoctrine()->getManager()->remove($product);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash(
            "success",
            "Votre produit ont été supprimé avec succès."
        );
        return $this->redirectToRoute("product_index");
    }
}
