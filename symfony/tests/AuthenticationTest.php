<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class AuthenticationTest extends ApiTestCase
{
    /**
     * Teste une tentative de connexion avec de mauvais identifiants.
     */
    public function testLoginWithWrongCredentials(): void
    {
        static::createClient()->request('POST', '/api/login_check', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => 'wrong@user.com',
                'password' => 'wrongpassword',
            ],
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    /**
     * Teste la connexion réussie et la récupération d'un token JWT.
     */
    public function testLoginSuccessAndGetToken(): void
    {
        $client = static::createClient();

        $response = $client->request('POST', '/api/login_check', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => 'correction',
                'password' => 'correction',
            ],
        ]);

        $json = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);
    }

    /**
     * Teste l'accès à une route protégée sans être authentifié.
     */
    public function testAccessProtectedResourceWithoutToken(): void
    {
        static::createClient()->request('GET', '/api/users');

        $this->assertResponseStatusCodeSame(401);
    }
}