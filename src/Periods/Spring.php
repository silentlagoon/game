<?php

namespace App\Periods;

class Spring extends BasePeriod
{
    protected array $healValueRanges = [5, 10];
    protected array $collapseValueRanges = [5];
    protected array  $durabilityValueRanges = [10];
}