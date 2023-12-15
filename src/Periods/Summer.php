<?php

namespace App\Periods;

class Summer extends BasePeriod
{
    protected array $collapseValueRanges = [5];
    protected array  $durabilityValueRanges = [10];
}