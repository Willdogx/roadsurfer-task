<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\ActivityDTO;
use App\Entity\ActivityType;
use App\Exception\ActivityType\ActivityTypeNotFoundException;
use App\Service\ActivityServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class ActivityController
{
    public function __construct(
        private ActivityServiceInterface $activityService,
        private SerializerInterface $serializer
    ) {}

    #[Route('/activity', name: 'activity_create', methods: ['POST'], format: 'json')]
    public function createActivity(#[MapRequestPayload] ActivityDTO $activityDto): Response
    {
        $this->activityService->createActivity($activityDto);

        return new Response(null, 201);
    }

    #[Route('/activity', name: 'activity_get', methods: ['GET'], format: 'json')]
    public function getActivities(#[MapQueryParameter] ?string $activityType = null): Response
    {
        try {
            $activities = $this->activityService->getActivities($activityType);

            $response = $this->serializer->serialize($activities, 'json', [
                DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s',
                AbstractNormalizer::CALLBACKS => [
                    'activityType' => function (ActivityType $activityType): string {
                        return $activityType->getName();
                    },
                ]
            ]);
            
            return new Response($response, 200);
        } catch (ActivityTypeNotFoundException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        }
    }

    #[Route('/activity/{type}/total-distance', name: 'activity_type_total_distance', methods: ['GET'], format: 'json')]
    public function getActivityTotalDistance(string $type): Response
    {
        try {
            $totalDistance = $this->activityService->getTotalDistanceForActivityType($type);
            return new JsonResponse(['totalDistance' => $totalDistance], 200);
        } catch (ActivityTypeNotFoundException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        }
    }

    #[Route('/activity/{type}/total-elapsed-time', name: 'activity_type_total_elapsed_time', methods: ['GET'], format: 'json')]
    public function getActivityTotalElapsedTime(string $type): Response
    {
        try {
            $totalElapsedTime = $this->activityService->getTotalElapsedTimeForActivityType($type);
            return new JsonResponse(['totalElapsedTime' => $totalElapsedTime], 200);
        } catch (ActivityTypeNotFoundException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        }   
    }
}