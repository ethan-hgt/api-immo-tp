<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class UserWelcomeSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MailerInterface $mailer
    ) {}

    public function getSubscribedEvents(): array
    {
        // On écoute l'événement "postPersist" de Doctrine
        // C'est déclenché juste après qu'une entité est sauvegardée en BDD
        return [
            Events::postPersist,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        // On vérifie que l'entité créée est bien un Utilisateur
        if (!$entity instanceof User) {
            return;
        }

        // On prépare l'email
        $email = (new Email())
            ->from('no-reply@catalogue-immo.com')
            ->to($entity->getEmail())
            ->subject('Bienvenue sur API Immo !')
            ->text('Félicitations, votre compte a été créé avec succès. Vous pouvez maintenant vous connecter pour gérer vos appartements.');

        // On envoie
        $this->mailer->send($email);
    }
}