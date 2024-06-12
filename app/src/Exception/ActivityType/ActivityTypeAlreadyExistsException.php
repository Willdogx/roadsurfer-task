<?php

declare(strict_types=1);

namespace App\Exception\ActivityType;

class ActivityTypeAlreadyExistsException extends \Exception
{
    public function __construct(string $name)
    {
        parent::__construct(sprintf('activity `%s` already exists.', $name));
    }
}