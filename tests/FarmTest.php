<?php

namespace App\Tests;

use App\Entity\Farm;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Uid\Uuid;

/**
 * Class FarmTest
 * @package App\Tests
 */
class FarmTest extends WebTestCase
{
    use AuthenticationTrait;

    public function testSuccessfulFarmShow(): void
    {
        $client = static::createAuthenticatedClient("customer@email.com");

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
            "farm[address][position][longitude]" => 7.5,
            "farm[image][file]" => $this->createImage()
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    /**
     * @param array $formData
     * @param string $errorMessage
     * @dataProvider provideBadRequests
     */
    public function testFailedFarmUpdate(array $formData, string $errorMessage): void
    {
        $client = static::createAuthenticatedClient("producer@email.com");

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $crawler = $client->request(Request::METHOD_GET, $router->generate("farm_update"));

        $form = $crawler->filter("form[name=farm]")->form($formData);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorTextContains("span.form-error-message", $errorMessage);
    }

    /**
     * @return Generator
     */
    public function provideBadRequests(): Generator
    {
        yield [
            [
                "farm[name]" => "",
                "farm[description]" => "Description",
                "farm[address][address]" => "address",
                "farm[address][zipCode]" => "28000",
                "farm[address][city]" => "Chartres",
                "farm[address][position][latitude]" => 46.5,
                "farm[address][position][longitude]" => 7.5
            ],
            "Cette valeur ne doit pas être vide."
        ];

        yield [
            [
                "farm[name]" => "Exploitation",
                "farm[description]" => "",
                "farm[address][address]" => "address",
                "farm[address][zipCode]" => "28000",
                "farm[address][city]" => "Chartres",
                "farm[address][position][latitude]" => 46.5,
                "farm[address][position][longitude]" => 7.5
            ],
            "Cette valeur ne doit pas être vide."
        ];

        yield [
            [
                "farm[name]" => "Exploitation",
                "farm[description]" => "Description",
                "farm[address][address]" => "",
                "farm[address][zipCode]" => "28000",
                "farm[address][city]" => "Chartres",
                "farm[address][position][latitude]" => 46.5,
                "farm[address][position][longitude]" => 7.5
            ],
            "Cette valeur ne doit pas être vide."
        ];

        yield [
            [
                "farm[name]" => "Exploitation",
                "farm[description]" => "Description",
                "farm[address][address]" => "address",
                "farm[address][zipCode]" => "",
                "farm[address][city]" => "Chartres",
                "farm[address][position][latitude]" => 46.5,
                "farm[address][position][longitude]" => 7.5
            ],
            "Cette valeur ne doit pas être vide."
        ];

        yield [
            [
                "farm[name]" => "Exploitation",
                "farm[description]" => "Description",
                "farm[address][address]" => "address",
                "farm[address][zipCode]" => "28000",
                "farm[address][city]" => "",
                "farm[address][position][latitude]" => null,
                "farm[address][position][longitude]" => 7.5
            ],
            "Cette valeur ne doit pas être vide."
        ];

        yield [
            [
                "farm[name]" => "Exploitation",
                "farm[description]" => "Description",
                "farm[address][address]" => "address",
                "farm[address][zipCode]" => "28000",
                "farm[address][city]" => "",
                "farm[address][position][latitude]" => 48.5,
                "farm[address][position][longitude]" => null
            ],
            "Cette valeur ne doit pas être vide."
        ];

        yield [
            [
                "farm[name]" => "Exploitation",
                "farm[description]" => "Description",
                "farm[address][address]" => "address",
                "farm[address][zipCode]" => "fail",
                "farm[address][city]" => "Chartres",
                "farm[address][position][latitude]" => 48.5,
                "farm[address][position][longitude]" => 7.5
            ],
            "Code postal invalide."
        ];
    }

    public function testAccessDeniedFarmUpdate(): void
    {
        $client = static::createAuthenticatedClient("customer@email.com");

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $client->request(Request::METHOD_GET, $router->generate("farm_update"));

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testNonLoggedFarmUpdate(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $client->request(Request::METHOD_GET, $router->generate("farm_update"));

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
