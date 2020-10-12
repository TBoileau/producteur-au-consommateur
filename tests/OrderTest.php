<?php

namespace App\Tests;

use App\Entity\Customer;
use App\Entity\Farm;
use App\Entity\Order;
use App\Entity\Producer;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class OrderTest
 * @package App\Tests
 */
class OrderTest extends WebTestCase
{
    use AuthenticationTrait;

    public function testSuccessfulAcceptOrder(): void
    {
        $client = static::createAuthenticatedClient("producer@email.com");

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $producer = $entityManager->getRepository(Producer::class)->findOneByEmail("producer@email.com");

        $order = $entityManager->getRepository(Order::class)->findOneBy([
            "state" => "created",
            "farm" => $producer->getFarm()
        ]);

        $crawler = $client->request(Request::METHOD_GET, $router->generate("order_accept", [
            "id" => $order->getId()
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->filter("form[name=accept_order]")->form();
        $csrfToken = $form->get("accept_order")["_token"]->getValue();

        $client->request(Request::METHOD_POST, $router->generate("order_accept", [
            "id" => $order->getId()
        ]), [
            "accept_order" => [
                "_token" => $csrfToken,
                "slots" => [
                    [
                        "startedAt" => "2021-01-01 10:00:00"
                    ],
                    [
                        "startedAt" => "2021-01-02 10:00:00"
                    ]
                ]
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $entityManager->clear();

        $order = $entityManager->getRepository(Order::class)->find($order->getId());

        $this->assertEquals("accepted", $order->getState());
        $this->assertCount(2, $order->getSlots());
    }

    public function testSuccessfulSettleOrder(): void
    {
        $client = static::createAuthenticatedClient("producer@email.com");

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $producer = $entityManager->getRepository(Producer::class)->findOneByEmail("producer@email.com");

        $order = $entityManager->getRepository(Order::class)->findOneBy([
            "state" => "accepted",
            "farm" => $producer->getFarm()
        ]);

        $client->request(Request::METHOD_GET, $router->generate("order_settle", [
            "id" => $order->getId()
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $entityManager->clear();

        $order = $entityManager->getRepository(Order::class)->find($order->getId());

        $this->assertEquals("settled", $order->getState());
    }

    public function testSuccessfulRefuseOrder(): void
    {
        $client = static::createAuthenticatedClient("producer@email.com");

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $client->request(Request::METHOD_GET, $router->generate("order_manage"));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $producer = $entityManager->getRepository(Producer::class)->findOneByEmail("producer@email.com");

        $order = $entityManager->getRepository(Order::class)->findOneBy([
            "state" => "created",
            "farm" => $producer->getFarm()
        ]);

        $client->request(Request::METHOD_GET, $router->generate("order_refuse", [
            "id" => $order->getId()
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $entityManager->clear();

        $order = $entityManager->getRepository(Order::class)->find($order->getId());

        $this->assertEquals("refused", $order->getState());
    }

    public function testSuccessfulCreateOrderAndCancelIt(): void
    {
        $client = static::createAuthenticatedClient("customer@email.com");

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $product = $entityManager->getRepository(Product::class)->findOneBy([]);

        $client->request(Request::METHOD_GET, $router->generate("cart_add", [
            "id" => $product->getId()
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->request(Request::METHOD_GET, $router->generate("order_create"));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $customer = $entityManager->getRepository(Customer::class)->findOneByEmail("customer@email.com");

        $order = $entityManager->getRepository(Order::class)->findOneBy([
            "state" => "created",
            "customer" => $customer
        ]);

        $client->request(Request::METHOD_GET, $router->generate("order_cancel", [
            "id" => $order->getId()
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $entityManager->clear();

        $order = $entityManager->getRepository(Order::class)->find($order->getId());

        $this->assertEquals("canceled", $order->getState());
    }

    public function testAccessDeniedCancelOrder(): void
    {
        $client = static::createAuthenticatedClient("producer@email.com");

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $order = $entityManager->getRepository(Order::class)->findOneBy(["state" => "created"]);

        $client->request(Request::METHOD_GET, $router->generate("order_cancel", [
            "id" => $order->getId()
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAccessDeniedCreateOrder(): void
    {
        $client = static::createAuthenticatedClient("producer@email.com");

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $client->request(Request::METHOD_GET, $router->generate("order_create"));

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testNonLoggedCreateOrder(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $client->request(Request::METHOD_GET, $router->generate("order_create"));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertRouteSame("security_login");
    }
}
