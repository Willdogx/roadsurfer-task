<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\DTO\ActivityTypeDTO;
use App\Entity\ActivityType;
use App\Exception\ActivityType\ActivityTypeAlreadyExistsException;
use App\Repository\ActivityTypeRepository;
use App\Service\ActivityTypeService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ActivityTypeServiceTest extends TestCase
{
    private ActivityTypeService $activityTypeService;
    private ActivityTypeRepository $activityTypeRepository;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->activityTypeRepository = $this->getMockBuilder(ActivityTypeRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->entityManager = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->activityTypeService = new ActivityTypeService(
            $this->activityTypeRepository,
            $this->entityManager
        );
    }


    public function testCreateActivityTypeThrowsException(): void
    {
        $this->expectException(ActivityTypeAlreadyExistsException::class);
        $this->expectExceptionMessage('activity `Running` already exists.');

        $this->activityTypeRepository->method('findOneBy')
            ->with(['name' => 'Running'])
            ->willReturn(new ActivityType());

        $activityTypeDto = new ActivityTypeDTO('Running');
    
        $this->activityTypeService->createActivityType($activityTypeDto);
    }

    public function testCreateActivityTypePersistsNewActivityType(): void
    {
        $expectedActivityType = new ActivityType();
        $expectedActivityType->setName('name');

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($expectedActivityType));
        $this->entityManager->expects($this->once())
            ->method('flush');

        $activityTypeDto = new ActivityTypeDTO('name');
        $this->activityTypeService->createActivityType($activityTypeDto);
    }
}