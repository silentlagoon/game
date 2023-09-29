<?php

namespace App\Periods;

use App\Periods\Contracts\IPeriod;

class TimesOfYear
{
    protected IPeriod $currentPeriod;
    protected int $currentYear = 1;

    public function __construct(IPeriod $startingPeriod = null)
    {
        $this->currentPeriod = $startingPeriod ?? new Spring();
    }

    /**
     * @return IPeriod
     */
    public function getCurrentPeriod(): IPeriod
    {
        return $this->currentPeriod;
    }

    /**
     * @return IPeriod
     */
    public function moveToNextPeriod(): IPeriod
    {
        $currentPeriods = $this->getPeriods();
        $nextPeriodKey = array_search($this->currentPeriod, $currentPeriods) + 1;

        if (isset($currentPeriods[$nextPeriodKey])) {
            return $this->currentPeriod = $currentPeriods[$nextPeriodKey];
        }

        $this->incrementYear();
        return $this->currentPeriod = reset($currentPeriods);
    }

    /**
     * @return array
     */
    public function getPeriods(): array
    {
        return [new Spring(), new Summer(), new Autumn(), new Winter()];
    }

    /**
     * @return bool
     */
    public function isSpring(): bool
    {
        return $this->currentPeriod instanceof Spring;
    }

    /**
     * @return bool
     */
    public function isWinter(): bool
    {
        return $this->currentPeriod instanceof Winter;
    }

    public function incrementYear(): void
    {
        $this->currentYear += 1;
    }

    public function getCurrentYear(): int
    {
        return $this->currentYear;
    }
}