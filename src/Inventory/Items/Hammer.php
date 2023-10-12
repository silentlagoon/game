<?php

namespace App\Inventory\Items;

use App\Inventory\Items\Contracts\IGiveGoldIncome;
use App\Inventory\Items\Contracts\IInventoryItem;
class Hammer implements IInventoryItem,IGiveGoldIncome
{
    protected int $incomeModifier = 2;
    protected int $maxHitPoints = 30;
    protected int $currentHitPoints = 0;
    protected int $weight = 3;

    public function getWeightValue(): int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): void
    {
        $this->weight = $weight;
    }

    protected function getMaxHitPoints(): int
    {
        return $this->maxHitPoints;
    }
    protected function getCurrentHitPoints(): int
    {
        return $this->maxHitPoints - $this->currentHitPoints;
    }

    public function getIncomeModifier(): int
    {
        return $this->incomeModifier;
    }

    public function setIncomeModifier(int $incomeModifier): void
    {
        $this->incomeModifier = $incomeModifier;
    }

}