<?php

namespace App\Controller;

use App\Event\DocumentIssuedEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class TestEventController extends AbstractController
{
    #[Route('/test/dispatch', name: 'test_dispatch')]
    public function testDispatch(EventDispatcherInterface $dispatcher): Response
    {
        // Simulate that user with ID 1 received a document
        $event = new DocumentIssuedEvent(
            userId: 1,
            documentTitle: 'Bachelor of Computer Science'
        );

        $dispatcher->dispatch($event);

        return new Response('Event dispatched!');
    }
}
