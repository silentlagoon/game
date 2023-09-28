<?php

namespace App\Periods;

class Winter extends BasePeriod
{
    protected array $damageValueRanges = [10, 15, 20];
    protected array $collapseValueRanges = [5];
}