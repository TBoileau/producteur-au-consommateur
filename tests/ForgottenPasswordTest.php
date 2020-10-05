<?php

namespace App\Tests;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class ForgottenPasswordTest
 * @package App\Tests
 */
class ForgottenPasswordTest extends WebTestCase
{
    /**
     * @param string $email
     * @dataProvider provideEmails
     */
    public function testSuccessfulForgottenPassword(string $email): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $crawler = $client->request(Request::METHOD_GET, $router->generate("security_forgotten_password"));

        $form = $crawler->filter("form[name=forgotten_password]")->form([
            "forgotten_password[email]" => $email
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneByEmail($email);

        $crawler = $client->request(Request::METHOD_GET, $router->generate("security_reset_password", [
            "token" => $user->getForgottenPassword()->getToken()
        ]));

        $form = $crawler->filter("form[name=reset_password]")->form([
            "reset_password[plainPassword]" => "password"
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    /**
     * @return Generator
     */
    public function provideEmails(): Generator
    {
        yield ["producer@email.com"];
        yield ['customer@email.com'];
    }

    /**
     * @param string $email
     * @param string $errorMessage
     * @dataProvider provideBadRequests
     */
    public function testBadRequest(string $email, string $errorMessage): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $crawler = $client->request(Request::METHOD_GET, $router->generate("security_forgotten_password"));

        $form = $crawler->filter("form[name=forgotten_password]")->form([
            "forgotten_password[email]" => $email
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorTextContains("span.form-error-message", $errorMessage);
    }

    /**
     * @return Generator
     */
    public function provideBadRequests(): Generator
    {
        yield ["fail@email.com", "Cette adresse email n'existe pas."];
    }
}
