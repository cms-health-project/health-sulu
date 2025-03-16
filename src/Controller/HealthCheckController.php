<?php

namespace CmsHealthProject\Controller;

use CmsHealthProject\Service\AuthService;
use CmsHealthProject\Service\HealthProviderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class HealthCheckController extends AbstractController
{

    // #[Route('%env(HEALTH_CHECK_PATH)%', name: 'health_status')]
    #[Route('/h12', name: 'health_status')]
    public function healthStatus(
        Request $request,
        AuthService $authService,
        HealthProviderService $healthProviderService
    ): JsonResponse {
        $authHeader = $request->headers->get('Authorization', null);
        if (is_null($authHeader)) {
            throw new BadRequestException('Authorization header is missing');
        }

        if (!str_starts_with($authHeader, 'Bearer ')) {
            throw new BadRequestException('The authorization header must start with Bearer');
        }
        $bearerToken = substr($authHeader, 7);

        if (!$authService->isTokenValid($bearerToken)) {
            throw new BadRequestException('Invalid authorization token');
        }


        // TODO: use the names-GET parameter of the request
        $requestedNames = [];

        $healthData = $healthProviderService->getHealthData($requestedNames);

        $response = new JsonResponse();
        $response->setData((array)$healthData);

        return $response;
    }
}
