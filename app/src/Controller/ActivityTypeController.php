<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\ActivityTypeDTO;
use App\Exception\ActivityType\ActivityTypeAlreadyExistsException;
use App\Service\ActivityTypeServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ActivityTypeController
{
    public function __construct(
        private ActivityTypeServiceInterface $activityTypeService
    ) {}

    #[Route('/activity-type', name: 'activity-type-create', methods: ['POST'], format: 'json')]
    public function createActivityType(#[MapRequestPayload] ActivityTypeDTO $activityTypeDto): Response
    {
        try {
            $this->activityTypeService->createActivityType($activityTypeDto);
    
            return new Response(null, 201);
        } catch (ActivityTypeAlreadyExistsException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 409);
        }
    }
}