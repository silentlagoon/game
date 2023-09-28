<?php

namespace App\Entities;

use App\Entities\Contracts\IEntity;
use App\Periods\Contracts\IPeriod;

abstract class BaseEntity implements IEntity
{
    protected string $name;

    protected bool $canBeDamagedByNature;
    protected bool $canBeHealedByNature;

    protected int $maxHitPoints;
    protected int $currentHitPoints;

    public function digestPeriod(IPeriod $period): array
    {
        $natureDamage = $this->processNatureDamage($period);

        return $natureDamage;
    }

    /**
     * @param IPeriod $period
     * @return array
     */
    protected function processNatureDamage(IPeriod $period): array
    {
        $damageReceived = $period->getNatureDamage();
        $damageHealed = $period->getNatureHealing();

        if ($this->canBeDamagedByNature) {
            $this->receiveDamage($period->getNatureDamage());
        }

        if ($this->canBeHealedByNature) {
            $this->regenerateDamage($period->getNatureHealing());
        }

        $resultHitPoints = sprintf('%s / %s', $this->getCurrentHitPoints(), $this->getMaxHitPoints());

        return compact('damageReceived', 'damageHealed', 'resultHitPoints');
    }

    public function getCurrentHitPoints(): int
    {
        return $this->currentHitPoints;
    }

    public function getMaxHitPoints(): int
    {
        return $this->maxHitPoints;
    }

    public function setCurrentHitPoints(int $hitPoints): void
    {
        $this->currentHitPoints = $hitPoints;
    }

    public function receiveDamage(int $hitPoints): int
    {
        $resultHitPointValue = $this->currentHitPoints - $hitPoints;
        return $this->currentHitPoints = max($resultHitPointValue, 0);
    }

    public function regenerateDamage(int $hitPoints): int
    {
        $resultHitPointValue = $this->currentHitPoints + $hitPoints;
        return $this->currentHitPoints = min($resultHitPointValue, $this->maxHitPoints);
    }

    public function isDead(): bool
    {
        return $this->currentHitPoints === 0;
    }

    public function getName(): string
    {
        return $this->name ?? (new \ReflectionClass($this))->getShortName();
    }
}