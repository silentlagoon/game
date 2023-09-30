<?php

namespace App;

use App\Periods\Contracts\IPeriod;

class GameDate
{
    protected int $year;
    protected IPeriod $period;
    protected int $day;

    public function __construct(int $year, IPeriod $period, $day)
    {
        $this->year = $year;
        $this->period = $period;
        $this->day = $day;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getPeriod(): IPeriod
    {
        return $this->period;
    }

    public function getDay(): int
    {
        return $this->day;
    }
}