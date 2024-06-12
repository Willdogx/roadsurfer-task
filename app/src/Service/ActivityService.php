<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ActivityDTO;
use App\Enum\DistanceUnit;
use App\Exception\ActivityType\ActivityTypeNotFoundException;
use App\Repository\ActivityTypeRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Activity;
use App\Repository\ActivityRepository;

class ActivityService implements ActivityServiceInterface
{
    public function __construct(
        private ActivityRepository $activityRepository,
        private ActivityTypeRepository $activityTypeRepository,
        private EntityManagerInterface $entityManager
    )
    {}

    public function createActivity(ActivityDTO $activityDto): void
    {
        $activityType = $this->activityTypeRepository->findOneBy(['name' => $activityDto->activityType]);
        if (!$activityType) {
            throw ActivityTypeNotFoundException::fromName($activityDto->activityType);
        }

        $activity = new Activity();
        $activity->setName($activityDto->name);
        $activity->setDistance($activityDto->distance);
        $activity->setDistanceUnit(DistanceUnit::from($activityDto->distanceUnit));
        $activity->setElapsedTime($activityDto->elapsedTime);
        $activity->setActivityType($activityType);
        $activity->setActivityDate(new \DateTime($activityDto->activityDate));

        $this->entityManager->persist($activity);
        $this->entityManager->flush();
    }

    /**
     * @return Activity[]
     */
    public function getActivities(?string $activityTypeName = null): array|Collection
    {
        if ($activityTypeName) {
            $activityType =  $this->activityTypeRepository->findOneBy(['name' => $activityTypeName]);
            if (!$activityType) {
                throw ActivityTypeNotFoundException::fromName($activityTypeName);
            }

            return $activityType->getActivities();
        }

        return $this->activityRepository->findAll();
    }

    /**
     * @return float total distance in km
     */
    public function getTotalDistanceForActivityType(string $activityTypeName): float
    {
        $activityType =  $this->activityTypeRepository->findOneBy(['name' => $activityTypeName]);
        if (!$activityType) {
            throw ActivityTypeNotFoundException::fromName($activityTypeName);
        }
        $total = 0;
        foreach ($activityType->getActivities() as $activity) {
            $distance = $activity->getDistance();
            if ($activity->getDistanceUnit() === DistanceUnit::M) {
                $distance = $distance / 1000;
            }
            $total += $distance;
        }
        return $total;
    }

        /**
     * @return int total elapsed time in seconds
     */
    public function getTotalElapsedTimeForActivityType(string $activityTypeName): int
    {
        $activityType =  $this->activityTypeRepository->findOneBy(['name' => $activityTypeName]);
        if (!$activityType) {
            throw ActivityTypeNotFoundException::fromName($activityTypeName);
        }
        $total = 0;
        foreach ($activityType->getActivities() as $activity) {
            $total += $activity->getElapsedTime();
        }
        return $total;
    }
}