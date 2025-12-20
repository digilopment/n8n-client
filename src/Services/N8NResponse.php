<?php

namespace Digilopment\N8NClient\Services;

class N8NResponse
{
    public function __construct(private array $data)
    {
        $this->parseTextJson();
    }

    private function parseTextJson(): void
    {
        $text = null;

        if (is_array($this->data) && !empty($this->data) && isset($this->data[0]['text'])) {
            $text = $this->data[0]['text'];
        } elseif (isset($this->data['text']) && is_string($this->data['text'])) {
            $text = $this->data['text'];
        }

        if ($text !== null && is_string($text)) {
            $parsed = json_decode($text, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
                $this->data = ['data' => $parsed, 'type' => 'json'];
            } else {
                $this->data = ['data' => $text, 'type' => 'text'];
            }
        }
    }

    public function getAll(): self
    {
        return $this;
    }

    public function getData(): self
    {
        echo json_encode($this->data);
        return $this;
    }

    public function ifError(array $errorData): self
    {
        if (!$this->isSuccess()) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode($errorData);
            exit;
        }
        return $this;
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
