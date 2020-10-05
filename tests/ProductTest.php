<?php

namespace App\Tests;

use App\Entity\Farm;
use App\Entity\Producer;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Uid\Uuid;

/**
 * Class ProductTest
 * @package App\Tests
 */
class ProductTest extends WebTestCase
{
    use AuthenticationTrait;

    public function testSuccessfulProductList(): void
    {
        $client = static::createAuthenticatedClient("producer@email.com");

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $client->request(Request::METHOD_GET, $router->generate("product_index"));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testSuccessfulProductDelete(): void
    {
        $client = static::createAuthenticatedClient("producer@email.com");

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $producer = $entityManager->getRepository(Producer::class)->findOneByEmail("producer@email.com");

        $farm = $entityManager->getRepository(Farm::class)->findOneByProducer($producer);

        $product = $entityManager->getRepository(Product::class)->findOneByFarm($farm);

        $client->request(Request::METHOD_GET, $router->generate("product_delete", [
            "id" => (string)$product->getId()
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testSuccessfulProductStock(): void
    {
        $client = static::createAuthenticatedClient("producer@email.com");

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $producer = $entityManager->getRepository(Producer::class)->findOneByEmail("producer@email.com");

        $farm = $entityManager->getRepository(Farm::class)->findOneByProducer($producer);

        $product = $entityManager->getRepository(Product::class)->findOneByFarm($farm);

        $crawler = $client->request(Request::METHOD_GET, $router->generate("product_stock", [
            "id" => (string)$product->getId()
        ]));

        $form = $crawler->filter("form[name=stock]")->form([
            "stock[quantity]" => 10
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testSuccessfulProductUpdate(): void
    {
        $client = static::createAuthenticatedClient("producer@email.com");

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $producer = $entityManager->getRepository(Producer::class)->findOneByEmail("producer@email.com");

        $farm = $entityManager->getRepository(Farm::class)->findOneByProducer($producer);

        $product = $entityManager->getRepository(Product::class)->findOneByFarm($farm);

        $crawler = $client->request(Request::METHOD_GET, $router->generate("product_update", [
            "id" => (string)$product->getId()
        ]));

        $form = $crawler->filter("form[name=product]")->form([
            "product[name]" => "Produit",
            "product[description]" => "Description",
            "product[price][unitPrice]" => 100,
            "product[price][vat]" => 2.1,
            "product[image][file]" => $this->createImage()
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testSuccessfulProductCreate(): void
    {
        $client = static::createAuthenticatedClient("producer@email.com");

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $crawler = $client->request(Request::METHOD_GET, $router->generate("product_create"));

        $form = $crawler->filter("form[name=product]")->form([
            "product[name]" => "Produit",
            "product[description]" => "Description",
            "product[price][unitPrice]" => 100,
            "product[price][vat]" => 2.1,
            "product[image][file]" => $this->createImage()
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    /**
     * @param array $formData
     * @param string $errorMessage
     * @dataProvider provideBadRequests
     */
    public function testFailedProductUpdate(array $formData, string $errorMessage): void
    {
        $client = static::createAuthenticatedClient("producer@email.com");

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $producer = $entityManager->getRepository(Producer::class)->findOneByEmail("producer@email.com");

        $farm = $entityManager->getRepository(Farm::class)->findOneByProducer($producer);

        $product = $entityManager->getRepository(Product::class)->findOneByFarm($farm);

        $crawler = $client->request(Request::METHOD_GET, $router->generate("product_update", [
            "id" => (string)$product->getId()
        ]));

        $form = $crawler->filter("form[name=product]")->form($formData);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorTextContains("span.form-error-message", $errorMessage);
    }

    /**
     * @param array $formData
     * @param string $errorMessage
     * @dataProvider provideBadRequests
     */
    public function testFailedProductCreate(array $formData, string $errorMessage): void
    {
        $client = static::createAuthenticatedClient("producer@email.com");

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $crawler = $client->request(Request::METHOD_GET, $router->generate("product_create"));

        $form = $crawler->filter("form[name=product]")->form($formData);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorTextContains("span.form-error-message", $errorMessage);
    }

    public function provideBadRequests(): Generator
    {
        yield [
            [
                "product[name]" => "",
                "product[description]" => "Description",
                "product[price][unitPrice]" => 100,
                "product[price][vat]" => 2.1
            ],
            "Cette valeur ne doit pas être vide."
        ];

        yield [
            [
                "product[name]" => "Produit",
                "product[description]" => "",
                "product[price][unitPrice]" => 100,
                "product[price][vat]" => 2.1
            ],
            "Cette valeur ne doit pas être vide."
        ];

        yield [
            [
                "product[name]" => "Produit",
                "product[description]" => "Description",
                "product[price][unitPrice]" => null,
                "product[price][vat]" => 2.1
            ],
            "Cette valeur n'est pas valide."
        ];

        yield [
            [
                "product[name]" => "Produit",
                "product[description]" => "Description",
                "product[price][unitPrice]" => -1,
                "product[price][vat]" => 2.1
            ],
            "Cette valeur doit être supérieure à 0."
        ];
    }

    public function testAccessDeniedProductCreate()
    {
        $client = static::createAuthenticatedClient("customer@email.com");

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $client->request(Request::METHOD_GET, $router->generate("product_create"));

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAccessDeniedProductUpdate()
    {
        $client = static::createAuthenticatedClient("customer@email.com");

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $product = $entityManager->getRepository(Product::class)->findOneBy([]);

        $client->request(Request::METHOD_GET, $router->generate("product_update", [
            "id" => (string) $product->getId()
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAccessDeniedProductDelete()
    {
        $client = static::createAuthenticatedClient("customer@email.com");

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $product = $entityManager->getRepository(Product::class)->findOneBy([]);

        $client->request(Request::METHOD_GET, $router->generate("product_delete", [
            "id" => (string) $product->getId()
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAccessDeniedProducts()
    {
        $client = static::createAuthenticatedClient("customer@email.com");

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $client->request(Request::METHOD_GET, $router->generate("product_index"));

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testNoLoggedProductCreate()
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $client->request(Request::METHOD_GET, $router->generate("product_create"));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertRouteSame("security_login");
    }

    public function testNoLoggedProductUpdate()
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $product = $entityManager->getRepository(Product::class)->findOneBy([]);

        $client->request(Request::METHOD_GET, $router->generate("product_update", [
            "id" => (string) $product->getId()
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertRouteSame("security_login");
    }

    public function testNoLoggedProductDelete()
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $product = $entityManager->getRepository(Product::class)->findOneBy([]);

        $client->request(Request::METHOD_GET, $router->generate("product_delete", [
            "id" => (string) $product->getId()
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertRouteSame("security_login");
    }

    public function testNoLoggedProducts()
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $client->request(Request::METHOD_GET, $router->generate("product_index"));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();
        
        $this->assertRouteSame("security_login");
    }

    /**
     * @return UploadedFile
     */
    private function createImage(): UploadedFile
    {
        $filename = Uuid::v4() . '.png';
        copy(
            __DIR__ . '/../public/uploads/image.png',
            __DIR__ . '/../public/uploads/' . $filename
        );

        return new UploadedFile(
            __DIR__ . '/../public/uploads/' . $filename,
            $filename,
            'image/png',
            null,
            true
        );
    }
}
