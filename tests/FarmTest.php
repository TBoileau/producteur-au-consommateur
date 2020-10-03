<?php

namespace App\Tests;

use App\Entity\Farm;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class FarmTest
 * @package App\Tests
 */
class FarmTest extends WebTestCase
{
    use AuthenticationTrait;

    public function testSuccessfulFarmShow(): void
    {
        $client = static::createAuthenticatedClient("producer@email.com");

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $farm = $entityManager->getRepository(Farm::class)->findOneBy([]);

        $client->request(Request::METHOD_GET, $router->generate("farm_show", [
            "id" => $farm->getId()
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }


    public function testSuccessfulFarmAll(): void
    {
        $client = static::createAuthenticatedClient("producer@email.com");

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $client->request(Request::METHOD_GET, $router->generate("farm_all"));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testSuccessfulFarmUpdate(): void
    {
        $client = static::createAuthenticatedClient("producer@email.com");

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $crawler = $client->request(Request::METHOD_GET, $router->generate("farm_update"));

        $form = $crawler->filter("form[name=farm]")->form([
            "farm[name]" => "Exploitation",
            "farm[description]" => "Description",
            "farm[address][address]" => "address",
            "farm[address][zipCode]" => "28000",
            "farm[address][city]" => "Chartres",
            "farm[address][position][latitude]" => 46.5,
            "farm[address][position][longitude]" => 7.5
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }
}
