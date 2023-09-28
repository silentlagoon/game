<?php

namespace App\Entities\Contracts;

use App\Periods\Contracts\IPeriod;
use App\Profile\Profile;

interface IEntity
{
    public function digestPeriod(IPeriod $period, Profile $profile): array;
    public function getCurrentHitPoints(): int;
    public function setCurrentHitPoints(int $hitPoints): void;
    public function receiveDamage(int $hitPoints): int;
    public function regenerateDamage(int $hitPoints): int;
    public function isDead(): bool;
    public function getName(): string;
    public function setName($name): void;
    public function getEntityCost(): int;
}