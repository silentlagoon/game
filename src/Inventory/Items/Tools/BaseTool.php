<?php

namespace App\Inventory\Items\Tools;

use App\Inventory\Items\BaseItem;
use App\Inventory\Items\Contracts\IGiveGoldIncome;
use App\Inventory\Items\Contracts\IInventoryItem;

class BaseTool extends BaseItem implements IGiveGoldIncome , IInventoryItem
{
    protected int $incomeModifier = 0;
    protected float $modifier = 0;
    protected string $workType;

    public function getModifier(): float
    {
        return $this->modifier;
    }

    public function setModifier(float $modifier): void
    {
        $this->modifier = $modifier;
    }

    public function getWorkType(): string
    {
        return $this->workType;
    }

    public function getIncomeModifier(): int
    {
        return $this->incomeModifier;
    }

    public function setIncomeModifier(int $incomeModifier): void
    {
        $this->incomeModifier = $incomeModifier;
    }

    public function setIsBeingUsed(bool $value): void
    {
        $this->isBeingUsed = $value;
    }


}