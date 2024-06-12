<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\ActivityController;
use App\DTO\ActivityDTO;
use App\DTO\ActivityTypeDTO;
use App\Entity\Activity;
use App\Entity\ActivityType;
use App\Enum\DistanceUnit;
use App\Exception\ActivityType\ActivityTypeAlreadyExistsException;
use App\Exception\ActivityType\ActivityTypeNotFoundException;
use App\Service\ActivityServiceInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ActivityControllerTest extends TestCase
{
    private ActivityServiceInterface $activityService;
    private ActivityController $activityController;

    protected function setUp(): void
    {
        $this->activityService = $this->getMockBuilder(ActivityServiceInterface::class)
            ->getMock();
        
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $this->activityController = new ActivityController(
            $this->activityService,
            $serializer
        );
    }

    public function testCreateActivityReturns201OnSuccess(): void
    {
        $activityDto = new ActivityDTO(500, DistanceUnit::M->value, 60, 'Running');
        $this->activityService->expects($this->once())
            ->method('createActivity')
            ->with($activityDto);


        $expected = new Response(null, 201);
        $actual = $this->activityController->createActivity($activityDto);

        $this->assertEquals($expected, $actual);
    }

    public function testGetActivitiesReturnsActivites(): void
    {
        $activityType = new ActivityType();
        $activityType->setName('Running');
        $activity = new Activity();
        $activity->setActivityType($activityType);

        $activities = [$activity];
        $this->activityService->method('getActivities')
            ->willReturn($activities);

        $expected = new Response(
            '[{"id":null,"name":null,"distance":null,"distanceUnit":null,"elapsedTime":null,"activityDate":null,"activityType":"Running"}]',
            200
        );
        $actual = $this->activityController->getActivities();

        $this->assertEquals($expected, $actual);
    }

    public function testGetActivitiesReturns404OnActivityTypeNotFound(): void
    {
        $this->activityService->method('getActivities')
            ->will($this->throwException(ActivityTypeNotFoundException::fromName('Running')));

        $expected = new JsonResponse(
            ['error' => 'activity `Running` not found.'],
            404
        );
        $actual = $this->activityController->getActivities();

        $this->assertEquals($expected, $actual);
    }

    public function testGetActivityTotalDistanceReturnsTotalDistance(): void
    {
        $this->activityService
            ->method('getTotalDistanceForActivityType')
            ->with('Running')
            ->willReturn((float) 5);
        
        $expected = new JsonResponse(
            ['totalDistance' => 5],
            200
        );
        $actual = $this->activityController->getActivityTotalDistance('Running');

        $this->assertEquals($expected, $actual);
    }

    public function testGetActivityTotalDistanceReturns404OnActivityTypeNotFound(): void
    {
        $this->activityService->method('getTotalDistanceForActivityType')
            ->will($this->throwException(ActivityTypeNotFoundException::fromName('Running')));

        $expected = new JsonResponse(
            ['error' => 'activity `Running` not found.'],
            404
        );
        $actual = $this->activityController->getActivityTotalDistance('Running');

        $this->assertEquals($expected, $actual);
    }

    public function testGetActivityTotalElapsedTimeReturnsTotalElapsedTime(): void
    {
        $this->activityService
            ->method('getTotalElapsedTimeForActivityType')
            ->with('Running')
            ->willReturn(120);
        
        $expected = new JsonResponse(
            ['totalElapsedTime' => 120],
            200
        );
        $actual = $this->activityController->getActivityTotalElapsedTime('Running');

        $this->assertEquals($expected, $actual);
    }

    public function testGetActivityTotalElapsedTimeReturns404OnActivityTypeNotFound(): void
    {
        $this->activityService->method('getTotalElapsedTimeForActivityType')
            ->will($this->throwException(ActivityTypeNotFoundException::fromName('Running')));

        $expected = new JsonResponse(
            ['error' => 'activity `Running` not found.'],
            404
        );
        $actual = $this->activityController->getActivityTotalElapsedTime('Running');

        $this->assertEquals($expected, $actual);
    }
}