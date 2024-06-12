<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\ActivityTypeController;
use App\DTO\ActivityTypeDTO;
use App\Exception\ActivityType\ActivityTypeAlreadyExistsException;
use App\Service\ActivityTypeServiceInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ActivityTypeControllerTest extends TestCase
{
    private ActivityTypeServiceInterface $activityTypeService;
    private ActivityTypeController $activityTypeController;

    protected function setUp(): void
    {
        $this->activityTypeService = $this->getMockBuilder(ActivityTypeServiceInterface::class)
            ->getMock();
        $this->activityTypeController = new ActivityTypeController(
            $this->activityTypeService
        );
    }

    public function testCreateActivityTypeReturns201OnSuccess(): void
    {
        $activityTypeDto = new ActivityTypeDTO('Running');
        $this->activityTypeService->expects($this->once())
            ->method('createActivityType')
            ->with($activityTypeDto);


        $expected = new Response(null, 201);
        $actual = $this->activityTypeController->createActivityType($activityTypeDto);

        $this->assertEquals($expected, $actual);
    }

    public function testCreateActivityTypeReturns409OnDuplicate(): void
    {
        $activityTypeDto = new ActivityTypeDTO('Running');
        $this->activityTypeService->expects($this->once())
            ->method('createActivityType')
            ->with($activityTypeDto)
            ->will($this->throwException(new ActivityTypeAlreadyExistsException('Running')));


        $expected = new JsonResponse(['error' => 'activity `Running` already exists.'], 409);
        $actual = $this->activityTypeController->createActivityType($activityTypeDto);

        $this->assertEquals($expected, $actual);
    }
}