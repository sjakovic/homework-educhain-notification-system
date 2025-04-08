<?php

namespace App\Service;

class NotificationPreferenceDefaults
{
    public static function getDefaults(): array
    {
        return [
            ['type' => 'document_issued',     'channel' => 'email'],
            ['type' => 'document_issued',     'channel' => 'in_app'],
            ['type' => 'request_approved',    'channel' => 'email'],
            ['type' => 'request_approved',    'channel' => 'in_app'],
            ['type' => 'credential_failed',   'channel' => 'email'],
            ['type' => 'credential_failed',   'channel' => 'in_app'],
            // add more as needed
        ];
    }
}
