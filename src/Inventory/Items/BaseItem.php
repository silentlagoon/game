<?php

namespace App\Inventory\Items;

use App\Inventory\Items\Contracts\IInventoryItem;
use App\Periods\Contracts\IPeriod;

abstract class BaseItem implements IInventoryItem
{
    protected string $name;
    protected int $maxHitPoints = 0;
    protected int $currentHitPoints = 0;
    protected int $weight = 0;
    protected int $coldProtection;

    protected bool $isEquipped = false;
    protected bool $isBeingUsed = false;

    public function isBroken(): bool
    {
        return $this->currentHitPoints === 0;
    }

    public function getName(): string
    {
        return $this->name ?? (new \ReflectionClass($this))->getShortName();
    }
    public function getWeightValue(): int
    {
        return $this->weight;
    }

    public function setWeightValue(int $weight): void
    {
        $this->weight = $weight;
    }

    public function getMaxHitPoints(): int
    {
        return $this->maxHitPoints;
    }
    public function getCurrentHitPoints(): int
    {
        return $this->maxHitPoints - $this->currentHitPoints;
    }

    public function isEquipped(): bool
    {
        return $this->isEquipped;
    }

    public function setIsEquipped(bool $value): void
    {
        $this->isEquipped = $value;
    }

    public function isBeingUsed(): bool
    {
        return $this->isBeingUsed;
    }

    public function setIsBeingUsed(bool $value): void
    {
        $this->isBeingUsed = $value;
    }

    public function receiveDamage(int $hitPoints): int
    {
        $resultHitPointValue = $this->currentHitPoints - $hitPoints;
        return $this->currentHitPoints = max($resultHitPointValue, 0);
    }

    public function processUseDamage(IPeriod $period): array
    {
        $damageReceived = $period->getDurabilityDamage();

        if ($this->isBeingUsed === true) {
            $this->receiveDamage($period->getDurabilityDamage());
        }

            $resultHitPoints = sprintf('%s / %s', $this->getCurrentHitPoints(), $this->getMaxHitPoints());
            return compact('damageReceived', 'resultHitPoints');
    }

}