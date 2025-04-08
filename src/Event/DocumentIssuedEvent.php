<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class DocumentIssuedEvent extends Event
{
    public function __construct(
        public readonly int $userId,
        public readonly string $documentTitle
    ) {}
}
