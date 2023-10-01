<?php

namespace App\Entities\Contracts;

use App\GameDate;
use App\Periods\Contracts\IPeriod;
use App\State\GameState;

interface IEntity
{
    public function digestPeriod(IPeriod $period, GameState $profile): void;
    public function setDateOfDeath(GameDate $date);
    public function getDateOfDeath(): ?GameDate;
    public function getCurrentHitPoints(): int;
    public function setCurrentHitPoints(int $hitPoints): void;
    public function receiveDamage(int $hitPoints): int;
    public function regenerateDamage(int $hitPoints): int;
    public function getCurrentHitPointsPercent(): int;
    public function isDead(): bool;
    public function getName(): string;
    public function setName($name): void;
    public function getGoldEarningsPerPeriod(): int;
    public function kill(GameDate $date);
    public function getCost(): int;
}