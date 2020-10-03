<?php

namespace App\Tests;

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

        $crawler = $client->request(Request::METHOD_GET, $router->generate("security_forgotten_password"));

        $form = $crawler->filter("form[name=forgotten_password]")->form([
            "forgotten_password[email]" => $email
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
}
