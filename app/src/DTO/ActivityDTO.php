<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enum\DistanceUnit;
use Symfony\Component\Validator\Constraints as Assert;

class ActivityDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\PositiveOrZero]
        public float $distance,
        
        #[Assert\NotBlank]
        #[Assert\Choice(callback: [DistanceUnit::class, 'values'])]
        public string $distanceUnit,
        
        #[Assert\NotBlank]
        #[Assert\PositiveOrZero]
        public int $elapsedTime,
        
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $activityType,
        
        #[Assert\Type('string')]
        public ?string $name = null,
        /** date must have Y-m-d H:i:s format */
        #[Assert\DateTime]
        public string $activityDate = ''
    ) {}
}