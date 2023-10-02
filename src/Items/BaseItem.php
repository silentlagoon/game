<?php

namespace App\Items;

use App\GameDate;
use App\Items\Contracts\IItem;
use App\Exceptions\Profile\NotEnoughGoldToSpendException;
use App\Periods\Contracts\IPeriod;
use App\State\GameState;
abstract class BaseItem implements IItem
{
    protected string $name;

    protected bool $isUsed;
    protected bool $isEquipped;
    protected bool $isConsumable;
    protected int $maxHitPoints;
    protected int $currentHitPoints;
    protected int $itemCost = 0;
    protected float $incomeIndex = 0;

    public function getName(): string
    {
        return $this->name;
    }

    public function getCurrentHitPoints(): int
    {
        return $this->currentHitPoints;
    }
    public function getCurrentHitPointsPercent(): int
    {
        if ($this->getCurrentHitPoints() > 0) {
            return (int) (($this->getCurrentHitPoints() / $this->getMaxHitPoints()) * 100);
        }
        return 0;
    }

    public function receiveDamage(int $hitPoints): int
    {
        $resultHitPointValue = $this->currentHitPoints - $hitPoints;
        return $this->currentHitPoints = max($resultHitPointValue, 0);
    }

    public function isConsumable(): bool
    {
        return $this->isConsumable;
    }

    public function isEquipped(): bool
    {
        return $this->isEquipped;
    }

     public function isUsed(): bool
     {
         return $this->isUsed;
     }

    public function getItemCost(): int
    {
        return $this->itemCost;
    }

    public function getIncomeIndex(): float
    {
        return $this->incomeIndex;
    }

    public function getMaxHitPoints(): int
    {
        return $this->maxHitPoints;
    }

}