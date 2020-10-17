<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\CartType;
use App\Handler\AcceptOrderHandler;
use App\Handler\CartHandler;
use App\HandlerFactory\HandlerFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class CartController
 * @package App\Controller
 * @Route("/cart")
 * @IsGRanted("ROLE_CUSTOMER")
 */
class CartController extends AbstractController
{
    /**
     * @param Product $product
     * @return RedirectResponse
     * @Route("/add/{id}", name="cart_add")
     * @IsGranted("add_to_cart", subject="product")
     */
    public function add(Product $product): RedirectResponse
    {
        $this->getUser()->addToCart($product);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash("success", "Le produit a bien été ajouté à votre panier.");
        return $this->redirectToRoute("farm_show", [
            "slug" => $product->getFarm()->getSlug()
        ]);
    }

    /**
     * @param Request $request
     * @param HandlerFactoryInterface $handlerFactory
     * @return Response
     * @Route("/", name="cart_index")
     */
    public function index(Request $request, HandlerFactoryInterface $handlerFactory): Response
    {
        $handler = $handlerFactory->createHandler(CartHandler::class);

        if ($handler->handle($request, $this->getUser())) {
            return $this->redirectToRoute("cart_index");
        }

        return $this->render("ui/cart/index.html.twig", [
            "form" => $handler->createView()
        ]);
    }
}
