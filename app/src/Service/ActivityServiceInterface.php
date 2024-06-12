<?php

namespace App\Service;

use App\DTO\ActivityDTO;
use Doctrine\Common\Collections\Collection;

interface ActivityServiceInterface
{
    public function createActivity(ActivityDTO $activityDto): void;
    public function getActivities(?string $activityTypeName = null): array|Collection;
    public function getTotalDistanceForActivityType(string $activityTypeName): float;
    public function getTotalElapsedTimeForActivityType(string $activityTypeName): int;
}