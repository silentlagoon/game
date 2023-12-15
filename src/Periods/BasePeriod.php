<?php

namespace App\Periods;

use App\Periods\Contracts\IPeriod;

abstract class BasePeriod implements IPeriod
{
    protected int $durationInDays = 5;
    protected string $name;
    protected array $healValueRanges = [];
    protected array $damageValueRanges = [];
    protected array $wearValueRanges = [];
    protected array $collapseValueRanges = [];
    protected array $durabilityValueRanges = [];

    public function getDurationInDays(): int
    {
        return $this->durationInDays;
    }

    public function getNatureHealing(): int
    {
        return $this->calculateChangeOfRange($this->healValueRanges);
    }

    public function getNatureDamage(): int
    {
        return $this->calculateChangeOfRange($this->damageValueRanges);
    }

    public function getCollapseDamage(): int
    {
        return $this->calculateChangeOfRange($this->collapseValueRanges);
    }

<<<<<<< Updated upstream
    public function getWearDamage(): int
    {
        return $this->calculateChangeOfRange($this->wearValueRanges);
    }
=======
    public function getDurabilityDamage(): int
    {
        return $this->calculateChangeOfRange($this->durabilityValueRanges);
    }

>>>>>>> Stashed changes
    public function getName(): string
    {
        return $this->name ?? (new \ReflectionClass($this))->getShortName();
    }

    /**
     * @param array $changeValues
     * @return int
     */
    protected function calculateChangeOfRange(array $changeValues): int
    {
        if (empty($changeValues)) {
            return 0;
        }

        return $changeValues[array_rand($changeValues)];
    }
}