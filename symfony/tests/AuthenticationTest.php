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

    /**
     * Teste que l'envoi d'email est bien déclenché à la création d'un utilisateur.
     */
    public function testEmailIsSentOnUserCreation(): void
    {
        // Activation explicite du profiler pour capturer les emails
        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->enableProfiler();

        // On crée un nouvel utilisateur via l'API
        // On utilise un email unique pour éviter les erreurs de doublons
        $email = 'test.mail.'.uniqid().'@example.com';

        $client->request('POST', '/api/users', [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => [
                'email' => $email,
                'password' => 'password123',
                'roles' => ['ROLE_USER'],
            ],
        ]);

        // 1. On vérifie que la création a réussi (Code 201 Created)
        $this->assertResponseStatusCodeSame(201);

        // 2. On vérifie qu'un e-mail a bien été envoyé
        $this->assertEmailCount(1);

        // 3. On vérifie le contenu de l'email
        $emailMessage = $this->getMailerMessage();
        $this->assertEmailHeaderSame($emailMessage, 'To', $email);
        $this->assertEmailHeaderSame($emailMessage, 'Subject', 'Bienvenue sur API Immo !');
        $this->assertEmailTextBodyContains($emailMessage, 'compte a été créé');
    }
}
