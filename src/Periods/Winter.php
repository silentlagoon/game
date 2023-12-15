<?php

namespace App\Periods;

class Winter extends BasePeriod
{
    protected array $damageValueRanges = [10, 15, 20];
    protected array $collapseValueRanges = [5];
    protected array  $durabilityValueRanges = [10];
}