<?php

namespace Digilopment\N8NClient;

use Dotenv\Dotenv;

class Config
{
    private static bool $loaded = false;

    private static function loadEnv(): void
    {
        if (self::$loaded) {
            return;
        }

        $rootPath = self::findProjectRoot();
        if ($rootPath && file_exists($rootPath . '/.env')) {
            Dotenv::createImmutable($rootPath)->load();
        }

        self::$loaded = true;
    }

    private static function findProjectRoot(): ?string
    {
        $startDir = getcwd() ?: (isset($_SERVER['PWD']) ? $_SERVER['PWD'] : __DIR__);
        $currentDir = $startDir;

        $maxDepth = 10;
        $depth = 0;

        while ($currentDir !== '/' && $currentDir !== '' && $depth < $maxDepth) {
            if (file_exists($currentDir . '/composer.json') || file_exists($currentDir . '/.env')) {
                return $currentDir;
            }
            $parentDir = dirname($currentDir);
            if ($parentDir === $currentDir) {
                break;
            }
            $currentDir = $parentDir;
            $depth++;
        }

        return $startDir;
    }

    public static function BASE_URL(): string
    {
        self::loadEnv();
        return $_ENV['N8N_BASE_URL'] ?? '';
    }

    public static function BEARER_TOKEN(): ?string
    {
        self::loadEnv();
        return $_ENV['N8N_BEARER_TOKEN'] ?? '';
    }

    public static function ENVIRONMENT(): string
    {
        self::loadEnv();
        return $_ENV['N8N_ENVIRONMENT'] ?? '';
    }
}
