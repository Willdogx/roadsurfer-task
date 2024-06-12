<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ActivityTypeDTO;
use App\Entity\ActivityType;
use App\Exception\ActivityType\ActivityTypeAlreadyExistsException;
use App\Repository\ActivityTypeRepository;
use Doctrine\ORM\EntityManagerInterface;

class ActivityTypeService implements ActivityTypeServiceInterface
{
    public function __construct(
        private ActivityTypeRepository $activityTypeRepository,
        private EntityManagerInterface $entityManager
    )
    {}

    /**
     * @throws ActivityTypeAlreadyExistsException
     */
    public function createActivityType(ActivityTypeDTO $activityTypeDto): void
    {
        $activityType = $this->activityTypeRepository->findOneBy(['name' => $activityTypeDto->name]);
        if ($activityType) {
            throw new ActivityTypeAlreadyExistsException($activityTypeDto->name);
        }

        $activityType = new ActivityType();
        $activityType->setName($activityTypeDto->name);

        $this->entityManager->persist($activityType);
        $this->entityManager->flush();
    }
}