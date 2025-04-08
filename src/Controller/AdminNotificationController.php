<?php

namespace App\Controller;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/notifications/manual')]
class AdminNotificationController extends AbstractController
{
    public function __construct(private MailerInterface $mailer) {}

    #[Route('', methods: ['POST'])]
    public function sendManualNotification(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $userId = $data['userId'] ?? null;
        $type = $data['type'] ?? 'manual';
        $message = $data['message'] ?? 'You have a new notification.';
        $digest = $data['digest'] ?? false;
        $sendEmail = $data['sendEmail'] ?? false;

        if (!$userId) {
            return $this->json(['error' => 'Missing userId'], 400);
        }

        // Create in-app notification
        $notification = new Notification();
        $notification->setUserId($userId);
        $notification->setType($type);
        $notification->setMessage($message);
        $notification->setCreatedAt(new \DateTimeImmutable());
        $notification->setDigest($digest);
        $notification->setSent(false);

        $em->persist($notification);

        // Optionally send email
        if ($sendEmail) {
            // TODO: Replace with actual email + language from user entity
            $emailAddress = 'student@example.com';
            $language = 'es';

            $email = (new Email())
                ->from(new Address('no-reply@educhain.com', 'Educhain'))
                ->to($emailAddress)
                ->subject('Manual Notification')
                ->html("<p>{$message}</p>");

            $this->mailer->send($email);
        }

        $em->flush();

        return $this->json(['status' => 'Notification sent']);
    }
}
