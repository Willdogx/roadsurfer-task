<?php

declare(strict_types=1);

namespace App\Exception\ActivityType;

class ActivityTypeNotFoundException extends \Exception
{
    private function __construct($message)
    {
        parent::__construct($message);
    }

    public static function fromName(?string $name): self
    {
        return new self(sprintf('activity `%s` not found.', $name));
    }
}