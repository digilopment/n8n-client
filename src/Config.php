<?php

namespace App;

class Config
{
    public static function BASE_URL(): string
    {
        return $_ENV['N8N_BASE_URL'] ?? '';
    }

    public static function BEARER_TOKEN(): ?string
    {
        return $_ENV['N8N_BEARER_TOKEN'] ?? '';
    }

    public static function ENVIRONMENT(): string
    {
        return $_ENV['N8N_ENVIRONMENT'] ?? '';
    }
}
