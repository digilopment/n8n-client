<?php

namespace App\Services;

class N8NResponse
{
    public function __construct(private array $data)
    {
    }

    public function getAll(): array
    {
        return $this->data;
    }

    public function getJson(?string $key = null): mixed
    {
        if ($key === null) {
            return $this->data;
        }
        $value = $this->data[$key] ?? null;
        return is_string($value) ? json_decode($value, true) : $value;
    }

    public function getImage(string $key = 'image'): ?string
    {
        return $this->data[$key] ?? null;
    }

    public function isSuccess(): bool
    {
        return !isset($this->data['error']);
    }

    public function getError(): ?string
    {
        return $this->data['message'] ?? null;
    }
}
