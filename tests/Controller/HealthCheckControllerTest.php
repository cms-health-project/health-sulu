<?php

namespace CmsHealthProject\Tests\Controller;

use CmsHealthProject\Controller\HealthCheckController;
use CmsHealthProject\SerializableReferenceImplementation\CheckCollection;
use CmsHealthProject\Service\AuthService;
use CmsHealthProject\Service\HealthProviderService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class HealthCheckControllerTest extends TestCase
{
    public function testHealthStatusReturnsJsonResponse(): void
    {
        $accessToken = '123456789123456789123456789';
        $request     = new Request();
        $request->headers->set('Authorization', 'Bearer ' . $accessToken);
        $authServiceMock = $this->createMock(AuthService::class);
        $authServiceMock->method('isTokenValid')->willReturn(true);
        $healthProviderServiceMock = $this->createMock(HealthProviderService::class);
        $healthProviderServiceMock->method('getHealthData')->willReturn(new CheckCollection());

        $controller = new HealthCheckController();
        $response   = $controller->healthStatus($request, $authServiceMock, $healthProviderServiceMock);

        // Assertions
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertIsString($response->getContent(), 'Health status response should be a string');
    }
}
