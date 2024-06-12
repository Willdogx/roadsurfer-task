<?php

namespace App\Service;

use App\DTO\ActivityTypeDTO;

interface ActivityTypeServiceInterface 
{
    public function createActivityType(ActivityTypeDTO $activityTypeDto): void;
}