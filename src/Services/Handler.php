<?php

namespace App\Services;

class Handler
{
    private N8NClient $n8nClient;

    public function __construct(?N8NClient $n8nClient = null)
    {
        $this->n8nClient = $n8nClient ?? new N8NClient();
    }

    public function handle(string $workflowName, ?string $bearerToken = null, ?string $environment = null): void
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        $tema = $input['tema'] ?? null;

        if (!$tema) {
            $this->sendError('Chýba téma', 400);
            return;
        }

        if ($bearerToken !== null) {
            $this->n8nClient = new N8NClient(null, $bearerToken, $environment);
        }

        $response = $this->n8nClient->workflow($workflowName, $environment)->execute(['topic' => $tema]);

        if ($response->isSuccess()) {
            $rawData = $response->getAll();
            file_put_contents('debug.txt', print_r($rawData, true));
            echo json_encode($rawData);
        } else {
            $this->sendError($response->getError(), 500);
        }
    }

    private function sendError(string $message, int $statusCode = 500): void
    {
        http_response_code($statusCode);
        echo json_encode(['error' => $message]);
    }
}
