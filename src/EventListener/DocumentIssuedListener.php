<?php

namespace App\EventListener;

use App\Entity\Notification;
use App\Event\DocumentIssuedEvent;
use App\Repository\NotificationPreferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class DocumentIssuedListener
{
    public function __construct(
        private EntityManagerInterface $em,
        private MailerInterface $mailer,
        private Environment $twig,
        private NotificationPreferenceRepository $preferences,
        private TranslatorInterface $translator,
    ) {}

    public function __invoke(DocumentIssuedEvent $event): void
    {
        $userId = $event->userId;
        $type = 'document_issued';

        // Simulated for now — later get from User entity
        $language = 'es';
        $emailAddress = 'student@example.com';

        // 🧠 IN-APP NOTIFICATION
        $inAppPref = $this->preferences->findOneBy([
            'userId' => $userId,
            'type' => $type,
            'channel' => 'in_app',
            'enabled' => true,
        ]);

        if ($inAppPref) {
            $notification = new Notification();
            $notification->setUserId($userId);
            $notification->setType($type);
            $notification->setMessage("Your document '{$event->documentTitle}' has been issued.");
            $notification->setCreatedAt(new \DateTimeImmutable());
            $notification->setDigest($inAppPref->getFrequency() !== 'immediate');
            $notification->setSent(false);

            $this->em->persist($notification);
        }

        // 📧 EMAIL NOTIFICATION
        $emailPref = $this->preferences->findOneBy([
            'userId' => $userId,
            'type' => $type,
            'channel' => 'email',
            'enabled' => true,
        ]);

        if ($emailPref && $emailPref->getFrequency() === 'immediate') {
            // Translate subject
            $subject = $this->translator->trans(
                "notification.subject.$type",
                [],
                'messages',
                $language
            );

            // Render localized template
            $html = $this->twig->render("emails/notifications/{$type}.{$language}.html.twig", [
                'documentTitle' => $event->documentTitle
            ]);

            $email = (new Email())
                ->from(new Address('no-reply@educhain.com', 'Educhain'))
                ->to($emailAddress)
                ->subject($subject)
                ->html($html);

            $this->mailer->send($email);
        }

        $this->em->flush();
    }
}
