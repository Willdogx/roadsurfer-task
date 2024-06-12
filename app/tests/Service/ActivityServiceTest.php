<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\DTO\ActivityDTO;
use App\Entity\Activity;
use App\Entity\ActivityType;
use App\Enum\DistanceUnit;
use App\Exception\ActivityType\ActivityTypeNotFoundException;
use App\Repository\ActivityRepository;
use App\Repository\ActivityTypeRepository;
use App\Service\ActivityService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\DateTime;

class ActivityServiceTest extends TestCase
{
    private ActivityService $activityService;
    private ActivityRepository $activityRepository;
    private ActivityTypeRepository $activityTypeRepository;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->activityRepository = $this->getMockBuilder(ActivityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->activityTypeRepository = $this->getMockBuilder(ActivityTypeRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->entityManager = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->activityService = new ActivityService(
            $this->activityRepository,
            $this->activityTypeRepository,
            $this->entityManager
        );
    }


    public static function activityTypeProvider(): array
    {
        return [
            [''],
            ['Running'],
        ];
    }

    /**
     * @dataProvider activityTypeProvider
     */
    public function testCreateActivityThrowsException(?string $activityType): void
    {
        $this->expectException(ActivityTypeNotFoundException::class);
        $this->expectExceptionMessage('activity `' . $activityType . '` not found.');
        $activityDto = new ActivityDTO(500, DistanceUnit::M->value, 60, $activityType);
    
        $this->activityService->createActivity($activityDto);
    }

    public function testCreateActivityPersistsNewActivity(): void
    {
        $activityDate = new \DateTime('2024-06-12 00:00:00');
        $activityType = new ActivityType();
        $activityType->setName('Running');

        $this->activityTypeRepository->method('findOneBy')
            ->with(['name' => 'Running'])
            ->willReturn($activityType);

        $expectedActivity = new Activity();
        $expectedActivity->setName('name');
        $expectedActivity->setDistance(500);
        $expectedActivity->setDistanceUnit(DistanceUnit::M);
        $expectedActivity->setActivityType($activityType);
        $expectedActivity->setElapsedTime(90);
        $expectedActivity->setActivityDate($activityDate);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($expectedActivity));
        $this->entityManager->expects($this->once())
            ->method('flush');

        $activityDto = new ActivityDTO(500, DistanceUnit::M->value, 90, 'Running', 'name', '2024-06-12 00:00:00');
    
        $this->activityService->createActivity($activityDto);
    }

    public function testGetActivitiesThrowsException(): void
    {
        $this->expectException(ActivityTypeNotFoundException::class);
        $this->expectExceptionMessage('activity `Running` not found.');
    
        $this->activityService->getActivities('Running');
    }

    public function testGetActivitiesReturnsAllActivities(): void
    {
        $activities = [new Activity(), new Activity(), new Activity()];

        $this->activityRepository->method('findAll')
            ->willReturn($activities);

        $this->assertSame($activities, $this->activityService->getActivities());
    }

    public function testGetActivitiesReturnsActivitiesByType(): void
    {
        $activityType = new ActivityType();
        $activityType->addActivity(new Activity());
        $activityType->addActivity(new Activity());
        $activities = $activityType->getActivities();

        $this->activityTypeRepository->method('findOneBy')
            ->with(['name' => 'Running'])
            ->willReturn($activityType);

        $this->assertSame($activities, $this->activityService->getActivities('Running'));
    }

    public function testGetTotalDistanceForActivityTypeThrowsException(): void
    {
        $this->expectException(ActivityTypeNotFoundException::class);
        $this->expectExceptionMessage('activity `Running` not found.');
    
        $this->activityService->getTotalDistanceForActivityType('Running');
    }

    public function testGetTotalDistanceForActivityTypeReturnsTotalInKm(): void
    {
        $activityType = new ActivityType();
        $activityType->addActivity(
            (new Activity())->setDistance(1)->setDistanceUnit(DistanceUnit::KM)
        );
        $activityType->addActivity(
            (new Activity())->setDistance(500)->setDistanceUnit(DistanceUnit::M)
        );

        $this->activityTypeRepository->method('findOneBy')
            ->with(['name' => 'Running'])
            ->willReturn($activityType);


        $expected = 1.5;
        $actual = $this->activityService->getTotalDistanceForActivityType('Running');

        $this->assertSame($expected, $actual);
    }

    public function testGetTotalElapsedTimeForActivityTypeThrowsException(): void
    {
        $this->expectException(ActivityTypeNotFoundException::class);
        $this->expectExceptionMessage('activity `Running` not found.');
    
        $this->activityService->getTotalElapsedTimeForActivityType('Running');
    }

    public function testGetTotalElapsedTimeForActivityTypeReturnsTotalInSeconds(): void
    {
        $activityType = new ActivityType();
        $activityType->addActivity(
            (new Activity())->setElapsedTime(60)
        );
        $activityType->addActivity(
            (new Activity())->setElapsedTime(30)
        );

        $this->activityTypeRepository->method('findOneBy')
            ->with(['name' => 'Running'])
            ->willReturn($activityType);


        $expected = 90;
        $actual = $this->activityService->getTotalElapsedTimeForActivityType('Running');

        $this->assertSame($expected, $actual);
    }
}