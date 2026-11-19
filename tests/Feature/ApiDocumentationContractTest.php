<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiDocumentationContractTest extends TestCase
{
    public function test_openapi_and_postman_document_the_live_health_endpoint_contract(): void
    {
        $openApi = json_decode(
            file_get_contents(base_path('docs/openapi.yaml')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );
        $postman = json_decode(
            file_get_contents(base_path('docs/postman/CBT-API.postman_collection.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        $this->assertSame('3.1.0', $openApi['openapi']);
        $this->assertSame(['get'], array_keys($openApi['paths']['/api/health']));
        $this->assertSame(
            ['success' => true, 'service' => 'cbt-api'],
            $openApi['paths']['/api/health']['get']['responses']['200']['content']['application/json']['example'],
        );
        $this->assertFalse(
            $openApi['paths']['/api/health']['get']['responses']['200']['content']['application/json']['schema']['additionalProperties'],
        );
        $this->assertSame(
            'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
            $postman['info']['schema'],
        );
        $this->assertSame('http://localhost:8000', $postman['variable'][0]['value']);
        $this->assertSame('GET', $postman['item'][0]['request']['method']);
        $this->assertSame('{{baseUrl}}/api/health', $postman['item'][0]['request']['url']['raw']);
    }
}
