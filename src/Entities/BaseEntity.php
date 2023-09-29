<?php

namespace App\Entities;

use App\Entities\Contracts\IEntity;
use App\Exceptions\Profile\NotEnoughGoldToSpendException;
use App\Periods\Contracts\IPeriod;
use App\Profile\GameState;

abstract class BaseEntity implements IEntity
{
    protected string $name;

    protected bool $canBeDamagedByNature;
    protected bool $canBeHealedByNature;
    protected bool $canCollapse;

    protected int $earnsGoldPerPeriod = 0;

    protected int $maxHitPoints;
    protected int $currentHitPoints;

    protected int $entityCost = 0;

    /**
     * @param GameState $profile
     * @throws NotEnoughGoldToSpendException
     */
    public function __construct(GameState $profile)
    {
        $profile->spendGoldAmount($this->entityCost);
    }

    public function digestPeriod(IPeriod $period, GameState $profile): array
    {
        $natureDamage = $this->processNatureDamage($period);
        $collapseDamage = $this->processCollapseDamage($period);
        $earnings = $this->processEarnings($period);

        if ($earnings > 0) {
            $profile->addGoldAmount($earnings);
        }

        return compact('natureDamage', 'collapseDamage');
    }

    protected function processCollapseDamage(IPeriod $period): array
    {
        $damageReceived = $period->getCollapseDamage();

        if ($this->canCollapse) {
            $this->receiveDamage($period->getCollapseDamage());
        }

        $resultHitPoints = sprintf('%s / %s', $this->getCurrentHitPoints(), $this->getMaxHitPoints());
        return compact('damageReceived', 'resultHitPoints');
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

    protected function processEarnings(IPeriod $period): int
    {
        $totalEarnings = 0;

        if ($this->earnsGoldPerPeriod > 0) {
            $totalEarnings += $this->earnsGoldPerPeriod;
        }

        return $totalEarnings;
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

    public function setName($name): void
    {
        $this->name = ucfirst($name);
    }

    public function getEntityCost(): int
    {
        return $this->entityCost;
    }

    public function getGoldEarningsPerPeriod(): int
    {
        return $this->isDead() ? 0 : $this->earnsGoldPerPeriod;
    }
}