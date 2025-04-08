<?php

namespace App\Controller;

use App\Entity\NotificationPreference;
use App\Repository\NotificationPreferenceRepository;
use App\Service\NotificationPreferenceDefaults;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/preferences')]
class NotificationPreferenceController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function getPreferences(
        NotificationPreferenceRepository $repo,
        EntityManagerInterface $em
    ): JsonResponse {
        $userId = 1; // Simulated for now

        $existing = $repo->findBy(['userId' => $userId]);

        if (count($existing) === 0) {
            // Auto-create missing defaults
            foreach (NotificationPreferenceDefaults::getDefaults() as $def) {
                $pref = new NotificationPreference();
                $pref->setUserId($userId);
                $pref->setType($def['type']);
                $pref->setChannel($def['channel']);
                $pref->setFrequency('immediate');
                $pref->setEnabled(true);

                $em->persist($pref);
            }
            $em->flush();
            $existing = $repo->findBy(['userId' => $userId]);
        }

        $data = array_map(function (NotificationPreference $pref) {
            return [
                'id' => $pref->getId(),
                'type' => $pref->getType(),
                'channel' => $pref->getChannel(),
                'frequency' => $pref->getFrequency(),
                'enabled' => $pref->isEnabled(),
            ];
        }, $existing);

        return $this->json($data);
    }

    #[Route('', methods: ['PUT'])]
    public function updatePreferences(Request $request, EntityManagerInterface $em, NotificationPreferenceRepository $repo): JsonResponse
    {
        $userId = 1; // Simulated
        $payload = json_decode($request->getContent(), true);

        foreach ($payload as $item) {
            $pref = $repo->findOneBy([
                'userId' => $userId,
                'type' => $item['type'],
                'channel' => $item['channel']
            ]);

            if (!$pref) {
                $pref = new NotificationPreference();
                $pref->setUserId($userId);
                $pref->setType($item['type']);
                $pref->setChannel($item['channel']);
            }

            $pref->setEnabled($item['enabled'] ?? true);
            $pref->setFrequency($item['frequency'] ?? 'immediate');
            $em->persist($pref);
        }

        $em->flush();

        return $this->json(['status' => 'ok']);
    }
}
