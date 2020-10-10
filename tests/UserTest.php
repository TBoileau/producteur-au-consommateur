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
 * Class UserTest
 * @package App\Tests
 */
class UserTest extends WebTestCase
{
    use AuthenticationTrait;

    public function testSuccessfulEditInfo(): void
    {
        $client = static::createAuthenticatedClient("customer@email.com");

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $crawler = $client->request(Request::METHOD_GET, $router->generate("user_edit_info"));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->filter("form[name=user_info]")->form([
            "user_info[firstName]" => "firstName",
            "user_info[lastName]" => "lastName",
            "user_info[email]" => "email@email.com"
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    /**
     * @param array $formData
     * @param string $errorMessage
     * @dataProvider provideBadRequestsForEditInfo
     */
    public function testBadRequestForEditInfo(array $formData, string $errorMessage): void
    {
        $client = static::createAuthenticatedClient("customer@email.com");

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $crawler = $client->request(Request::METHOD_GET, $router->generate("user_edit_info"));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->filter("form[name=user_info]")->form($formData);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorTextContains("span.form-error-message", $errorMessage);
    }

    public function provideBadRequestsForEditInfo(): Generator
    {
        yield [
            [
                "user_info[firstName]" => "",
                "user_info[lastName]" => "lastName",
                "user_info[email]" => "email@email.com"
            ],
            "Cette valeur ne doit pas être vide."
        ];
        yield [
            [
                "user_info[firstName]" => "firstName",
                "user_info[lastName]" => "",
                "user_info[email]" => "email@email.com"
            ],
            "Cette valeur ne doit pas être vide."
        ];
        yield [
            [
                "user_info[firstName]" => "firstName",
                "user_info[lastName]" => "lastName",
                "user_info[email]" => ""
            ],
            "Cette valeur ne doit pas être vide."
        ];
        yield [
            [
                "user_info[firstName]" => "firstName",
                "user_info[lastName]" => "lastName",
                "user_info[email]" => "fail"
            ],
            "Cette valeur n'est pas une adresse email valide."
        ];
    }

    public function testSuccessfulEditPassword(): void
    {
        $client = static::createAuthenticatedClient("customer@email.com");

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $crawler = $client->request(Request::METHOD_GET, $router->generate("user_edit_password"));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->filter("form[name=user_password]")->form([
            "user_password[currentPassword]" => "password",
            "user_password[plainPassword][first]" => "password123",
            "user_password[plainPassword][second]" => "password123"
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    /**
     * @param array $formData
     * @param string $errorMessage
     * @dataProvider provideBadRequestsForEditPassword
     */
    public function testBadRequestForEditPassword(array $formData, string $errorMessage): void
    {
        $client = static::createAuthenticatedClient("customer@email.com");

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $crawler = $client->request(Request::METHOD_GET, $router->generate("user_edit_password"));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->filter("form[name=user_password]")->form($formData);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorTextContains("span.form-error-message", $errorMessage);
    }

    public function provideBadRequestsForEditPassword(): Generator
    {
        yield [
            [
                "user_password[currentPassword]" => "password",
                "user_password[plainPassword][first]" => "",
                "user_password[plainPassword][second]" => ""
            ],
            "Cette valeur ne doit pas être vide."
        ];
        yield [
            [
                "user_password[currentPassword]" => "password",
                "user_password[plainPassword][first]" => "password123",
                "user_password[plainPassword][second]" => "fail"
            ],
            "Le mot de passe et sa confirmation ne sont pas similaires."
        ];
        yield [
            [
                "user_password[currentPassword]" => "password",
                "user_password[plainPassword][first]" => "fail",
                "user_password[plainPassword][second]" => "fail"
            ],
            "Cette chaîne est trop courte. Elle doit avoir au minimum 8 caractères."
        ];
        yield [
            [
                "user_password[currentPassword]" => "fail",
                "user_password[plainPassword][first]" => "password123",
                "user_password[plainPassword][second]" => "password123"
            ],
            "Cette valeur doit être le mot de passe actuel de l'utilisateur."
        ];
    }
}
