<?php

namespace Digilopment\N8NClient\Services;

class Handler
{
    private N8NClient $n8nClient;

    private array $data = [];

    public function __construct(?N8NClient $n8nClient = null)
    {
        $this->n8nClient = $n8nClient ?? new N8NClient();
    }

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function handleJson(string $workflowName, ?string $bearerToken = null, ?string $environment = null): N8NResponse
    {
        header('Content-Type: application/json');

        if ($bearerToken !== null) {
            $this->n8nClient = new N8NClient(null, $bearerToken, $environment);
        }

        return $this->n8nClient->workflow($workflowName, $environment)->execute($this->data);
    }

    public function handleText(string $workflowName, ?string $bearerToken = null, ?string $environment = null): N8NResponse
    {
        header('Content-Type: text/plain');

        if ($bearerToken !== null) {
            $this->n8nClient = new N8NClient(null, $bearerToken, $environment);
        }

        return $this->n8nClient->workflow($workflowName, $environment)->execute($this->data);
    }
}
