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
        $this->assertSame(['/api/health'], array_keys($openApi['paths']));
        $this->assertSame(['get'], array_keys($openApi['paths']['/api/health']));
        $this->assertSame(
            ['success' => true, 'service' => 'cbt-api'],
            $openApi['paths']['/api/health']['get']['responses']['200']['content']['application/json']['example'],
        );
        $schema = $openApi['paths']['/api/health']['get']['responses']['200']['content']['application/json']['schema'];

        $this->assertSame('object', $schema['type']);
        $this->assertFalse($schema['additionalProperties']);
        $this->assertSame(['success', 'service'], $schema['required']);
        $this->assertEqualsCanonicalizing(['success', 'service'], array_keys($schema['properties']));
        $this->assertSame(
            ['type' => 'boolean', 'const' => true],
            $schema['properties']['success'],
        );
        $this->assertSame(
            ['type' => 'string', 'const' => 'cbt-api'],
            $schema['properties']['service'],
        );
        $this->assertSame(
            'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
            $postman['info']['schema'],
        );
        $this->assertCount(1, $postman['item']);
        $this->assertSame('Pemeriksaan kesehatan', $postman['item'][0]['name']);
        $this->assertSame('baseUrl', $postman['variable'][0]['key']);
        $this->assertSame('http://localhost:8000', $postman['variable'][0]['value']);
        $this->assertSame('GET', $postman['item'][0]['request']['method']);
        $this->assertSame('{{baseUrl}}/api/health', $postman['item'][0]['request']['url']['raw']);
    }
}
