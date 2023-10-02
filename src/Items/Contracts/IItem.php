<?php

namespace App\Items\Contracts;

use App\GameDate;
use App\Periods\Contracts\IPeriod;
use App\State\GameState;

interface IItem
{
    public function getCurrentHitPoints(): int;
    public function receiveDamage(int $hitPoints): int;
    public function getCurrentHitPointsPercent(): int;
    public function isConsumable(): bool;
    public  function isEquipped():bool;
    public function isUsed(): bool;
    public function getName(): string;
    public function getItemCost(): int;

}