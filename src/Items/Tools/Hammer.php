<?php

namespace App\Items\Tools;

use App\Items\Tools\Tool;

class Hammer extends Tool
{
    protected int $maxHitPoints = 20;
    protected int $currentHitPoints = 20;
    protected int $itemCost = 10;
    protected float  $incomeIndex = 2;


}