<?php

namespace App\Tests;

use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class RegistrationTest
 * @package App\Tests
 */
class RegistrationTest extends WebTestCase
{
    /**
     * @param string $role
     * @param array $formData
     * @dataProvider provideRoles
     */
    public function testSuccessfulRegistration(string $role, array $formData): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $crawler = $client->request(Request::METHOD_GET, $router->generate("security_registration", [
            "role" => $role
        ]));

        $form = $crawler->filter("form[name=registration]")->form($formData);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    /**
     * @return Generator
     */
    public function provideRoles(): Generator
    {
        yield ['producer', [
            "registration[email]" => "email@email.com",
            "registration[plainPassword]" => "password",
            "registration[firstName]" => "John",
            "registration[lastName]" => "Doe",
            "registration[farm][name]" => "Exploitation"
        ]];
        yield ['producer', [
            "registration[email]" => "email@email.com",
            "registration[plainPassword]" => "password",
            "registration[firstName]" => "John",
            "registration[lastName]" => "Doe",
            "registration[farm][name]" => "Ferme"
        ]];
        yield ['customer', [
            "registration[email]" => "email@email.com",
            "registration[plainPassword]" => "password",
            "registration[firstName]" => "John",
            "registration[lastName]" => "Doe"
        ]];
    }

    /**
     * @param array $formData
     * @param string $errorMessage
     * @dataProvider provideBadRequests
     */
    public function testBadRequest(string $role, array $formData, string $errorMessage): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $crawler = $client->request(Request::METHOD_GET, $router->generate("security_registration", [
            "role" => $role
        ]));

        $form = $crawler->filter("form[name=registration]")->form($formData);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorTextContains("span.form-error-message", $errorMessage);
    }

    public function provideBadRequests(): Generator
    {
        yield from $this->provideCustomerBadRequests();
        yield from $this->provideProducerBadRequests();
    }

    public function provideCustomerBadRequests(): Generator
    {
        yield [
            "customer",
            [
                "registration[email]" => "",
                "registration[plainPassword]" => "password",
                "registration[firstName]" => "John",
                "registration[lastName]" => "Doe"
            ],
            "Cette valeur ne doit pas être vide."
        ];

        yield [
            "customer",
            [
                "registration[email]" => "email@email.com",
                "registration[plainPassword]" => "",
                "registration[firstName]" => "John",
                "registration[lastName]" => "Doe"
            ],
            "Cette valeur ne doit pas être vide."
        ];

        yield [
            "customer",
            [
                "registration[email]" => "email@email.com",
                "registration[plainPassword]" => "password",
                "registration[firstName]" => "",
                "registration[lastName]" => "Doe"
            ],
            "Cette valeur ne doit pas être vide."
        ];

        yield [
            "customer",
            [
                "registration[email]" => "email@email.com",
                "registration[plainPassword]" => "password",
                "registration[firstName]" => "John",
                "registration[lastName]" => ""
            ],
            "Cette valeur ne doit pas être vide."
        ];

        yield [
            "customer",
            [
                "registration[email]" => "fail",
                "registration[plainPassword]" => "password",
                "registration[firstName]" => "John",
                "registration[lastName]" => "Doe"
            ],
            "Cette valeur n'est pas une adresse email valide."
        ];

        yield [
            "customer",
            [
                "registration[email]" => "email@email.com",
                "registration[plainPassword]" => "fail",
                "registration[firstName]" => "John",
                "registration[lastName]" => "Doe"
            ],
            "Cette chaîne est trop courte. Elle doit avoir au minimum 8 caractères."
        ];

        yield [
            "customer",
            [
                "registration[email]" => "producer@email.com",
                "registration[plainPassword]" => "password",
                "registration[firstName]" => "John",
                "registration[lastName]" => "Doe"
            ],
            "Il semblerait que vous soyez déjà inscrit."
        ];
    }

    public function provideProducerBadRequests(): Generator
    {
        yield [
            "producer",
            [
                "registration[email]" => "email@email.com",
                "registration[plainPassword]" => "password",
                "registration[firstName]" => "John",
                "registration[lastName]" => "Doe",
                "registration[farm][name]" => ""
            ],
            "Cette valeur ne doit pas être vide."
        ];

        yield [
            "producer",
            [
                "registration[email]" => "",
                "registration[plainPassword]" => "password",
                "registration[firstName]" => "John",
                "registration[lastName]" => "Doe",
                "registration[farm][name]" => "Ferme"
            ],
            "Cette valeur ne doit pas être vide."
        ];

        yield [
            "producer",
            [
                "registration[email]" => "email@email.com",
                "registration[plainPassword]" => "",
                "registration[firstName]" => "John",
                "registration[lastName]" => "Doe",
                "registration[farm][name]" => "Ferme"
            ],
            "Cette valeur ne doit pas être vide."
        ];

        yield [
            "producer",
            [
                "registration[email]" => "email@email.com",
                "registration[plainPassword]" => "password",
                "registration[firstName]" => "",
                "registration[lastName]" => "Doe",
                "registration[farm][name]" => "Ferme"
            ],
            "Cette valeur ne doit pas être vide."
        ];

        yield [
            "producer",
            [
                "registration[email]" => "email@email.com",
                "registration[plainPassword]" => "password",
                "registration[firstName]" => "John",
                "registration[lastName]" => "",
                "registration[farm][name]" => "Ferme"
            ],
            "Cette valeur ne doit pas être vide."
        ];

        yield [
            "producer",
            [
                "registration[email]" => "fail",
                "registration[plainPassword]" => "password",
                "registration[firstName]" => "John",
                "registration[lastName]" => "Doe",
                "registration[farm][name]" => "Ferme"
            ],
            "Cette valeur n'est pas une adresse email valide."
        ];

        yield [
            "producer",
            [
                "registration[email]" => "email@email.com",
                "registration[plainPassword]" => "fail",
                "registration[firstName]" => "John",
                "registration[lastName]" => "Doe",
                "registration[farm][name]" => "Ferme"
            ],
            "Cette chaîne est trop courte. Elle doit avoir au minimum 8 caractères."
        ];

        yield [
            "producer",
            [
                "registration[email]" => "producer@email.com",
                "registration[plainPassword]" => "password",
                "registration[firstName]" => "John",
                "registration[lastName]" => "Doe",
                "registration[farm][name]" => "Ferme"
            ],
            "Il semblerait que vous soyez déjà inscrit."
        ];
    }
}
