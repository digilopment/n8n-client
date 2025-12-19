# Digilopment N8N Client

PHP client library for n8n webhook integration with support for environment-based configuration.

## Installation

```bash
composer require digilopment/n8n-client
```

## Requirements

- PHP 8.0 or higher
- Guzzle HTTP Client 7.0+
- vlucas/phpdotenv 5.5+

## Configuration

Create a `.env` file in your project root:

```env
N8N_BASE_URL=https://your-n8n-instance.com
N8N_BEARER_TOKEN=your_bearer_token_here
N8N_ENVIRONMENT=production
```

Or use `.env.example` as a template.

## Usage

### Basic Usage

```php
<?php

require 'vendor/autoload.php';

use Digilopment\N8NClient\Services\N8NClient;

// Uses default values from .env file
$client = new N8NClient();

// Execute workflow
$response = $client->workflow('your-workflow-name')->execute([
    'key' => 'value',
    'data' => 'example'
]);

if ($response->isSuccess()) {
    $data = $response->getAll();
    echo json_encode($data);
} else {
    echo "Error: " . $response->getError();
}
```

### Custom Configuration

```php
use Digilopment\N8NClient\Services\N8NClient;

// Override base URL, bearer token, and environment
$client = new N8NClient(
    'https://custom-n8n-instance.com',
    'custom-bearer-token',
    'devel'
);

// Override environment for specific workflow
$response = $client->workflow('workflow-name', 'production')->execute(['data' => 'test']);
```

### Using Handler

```php
<?php

require 'vendor/autoload.php';

use Digilopment\N8NClient\Services\Handler;

$handler = new Handler();
$handler->handle('workflow-name');
// Optional: $handler->handle('workflow-name', 'bearer-token', 'environment');
```

### Response Methods

```php
$response = $client->workflow('name')->execute(['data' => 'test']);

// Get all data
$allData = $response->getAll();

// Get specific JSON value
$value = $response->getJson('key');

// Get image URL
$imageUrl = $response->getImage('image');

// Check if successful
if ($response->isSuccess()) {
    // Handle success
}

// Get error message
$error = $response->getError();
```

## Environment Variables

- `N8N_BASE_URL` - Base URL of your n8n instance
- `N8N_BEARER_TOKEN` - Bearer token for authentication
- `N8N_ENVIRONMENT` - Environment: `production` (uses `webhook/`) or `devel` (uses `webhook-test/`)

## Testing

```bash
composer test
```

## License

MIT

## Support

For issues and questions, please open an issue on GitHub.

