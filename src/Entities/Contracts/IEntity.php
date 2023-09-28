<?php

namespace App\Entities\Contracts;

use App\Periods\Contracts\IPeriod;

interface IEntity
{
    public function digestPeriod(IPeriod $period): array;
    public function getCurrentHitPoints(): int;
    public function setCurrentHitPoints(int $hitPoints): void;
    public function receiveDamage(int $hitPoints): int;
    public function regenerateDamage(int $hitPoints): int;
    public function isDead(): bool;
    public function getName(): string;
}