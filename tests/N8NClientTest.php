<?php

namespace Digilopment\N8NClient\Tests;

use Digilopment\N8NClient\Services\N8NClient;
use PHPUnit\Framework\TestCase;

class N8NClientTest extends TestCase
{
    public function testClientCanBeInstantiated()
    {
        $client = new N8NClient('https://n8n.example.com', 'secret_key');
        $this->assertInstanceOf(N8NClient::class, $client);
    }

    public function testWorkflowSetsCorrectPath()
    {
        $client = new N8NClient('https://n8n.example.com', null, 'production');
        $client->workflow('test-path');
        
        $reflection = new \ReflectionClass($client);
        $property = $reflection->getProperty('currentPath');
        $property->setAccessible(true);
        
        $this->assertEquals('webhook/test-path', $property->getValue($client));
    }

    public function testWorkflowSetsCorrectPathForDevel()
    {
        $client = new N8NClient('https://n8n.example.com', null, 'devel');
        $client->workflow('test-path');
        
        $reflection = new \ReflectionClass($client);
        $property = $reflection->getProperty('currentPath');
        $property->setAccessible(true);
        
        $this->assertEquals('webhook-test/test-path', $property->getValue($client));
    }
}
