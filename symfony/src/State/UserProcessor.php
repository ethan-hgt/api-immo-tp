<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * State Processor personnalisé pour gérer la création d'utilisateurs.
 * Ce processor :
 * - Hash le mot de passe
 * - Envoie un email de bienvenue
 */
final class UserProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private MailerInterface $mailer,
    ) {}

    /**
     * @param User $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        // Hash le mot de passe avant la persistence
        if ($data->getPassword()) {
            $hashedPassword = $this->passwordHasher->hashPassword(
                $data,
                $data->getPassword()
            );
            $data->setPassword($hashedPassword);
        }

        // Persiste l'utilisateur
        $this->entityManager->persist($data);
        $this->entityManager->flush();

        // Envoie l'email de bienvenue
        $email = (new Email())
            ->from('no-reply@catalogue-immo.com')
            ->to($data->getEmail())
            ->subject('Bienvenue sur API Immo !')
            ->text('Félicitations, votre compte a été créé avec succès. Vous pouvez maintenant vous connecter pour gérer vos appartements.');

        $this->mailer->send($email);

        return $data;
    }
}
