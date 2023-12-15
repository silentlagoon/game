<?php

namespace App\Inventory\Items\Contracts;

use App\Periods\Contracts\IPeriod;

interface IInventoryItem
{
    public function getWeightValue(): int;
<<<<<<< Updated upstream
=======
    public function setWeightValue(int $weight): void;
    public function getName(): string;
    public function getMaxHitPoints(): int;
    public function getCurrentHitPoints(): int;
    public function isEquipped(): bool;
    public function isBeingUsed(): bool;
    public function setIsBeingUsed(bool $value): void;
    public function receiveDamage(int $hitPoints): int;
    public function setIsEquipped(bool $value): void;
    public function processUseDamage(IPeriod $period): array;
    public function isBroken(): bool;

>>>>>>> Stashed changes
}