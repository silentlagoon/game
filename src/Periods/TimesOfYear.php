<?php

namespace App\Periods;

use App\Periods\Contracts\IPeriod;

class TimesOfYear
{
    protected IPeriod $currentPeriod;

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
        return $this->currentPeriod = $currentPeriods[$nextPeriodKey] ?? reset($currentPeriods);
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
}