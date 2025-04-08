<?php

namespace App\Controller;

use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\EntityManagerInterface;


class NotificationController extends AbstractController
{
    #[Route('/api/notifications', name: 'api_notifications', methods: ['GET'])]
    public function index(NotificationRepository $repository): JsonResponse
    {
        // Simulate user ID for now — in production you'd use $this->getUser()->getId()
        $userId = 1;

        $notifications = $repository->findByUserId($userId);

        // Return serialized data
        $data = array_map(function ($notification) {
            return [
                'id' => $notification->getId(),
                'type' => $notification->getType(),
                'message' => $notification->getMessage(),
                'readAt' => $notification->getReadAt()?->format('Y-m-d H:i:s'),
                'createdAt' => $notification->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
        }, $notifications);

        return $this->json($data);
    }

    #[Route('/api/notifications/{id}/read', name: 'api_notification_read', methods: ['POST'])]
    public function markAsRead(int $id, NotificationRepository $repository, EntityManagerInterface $em): JsonResponse
    {
        // Simulate auth (later: check $this->getUser()->getId())
        $userId = 1;

        $notification = $repository->find($id);

        if (!$notification || $notification->getUserId() !== $userId) {
            throw new NotFoundHttpException("Notification not found.");
        }

        if ($notification->getReadAt() === null) {
            $notification->markAsRead();
            $em->flush();
        }

        return $this->json(['status' => 'ok']);
    }
}
