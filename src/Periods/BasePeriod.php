<?php

namespace App\Periods;

use App\Periods\Contracts\IPeriod;

abstract class BasePeriod implements IPeriod
{
    protected string $name;
    protected array $healValueRanges = [];
    protected array $damageValuesRanges = [];

    public function getNatureHealing(): int
    {
        return $this->calculateChangeOfRange($this->healValueRanges);
    }

    public function getNatureDamage(): int
    {
        return $this->calculateChangeOfRange($this->damageValuesRanges);
    }

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