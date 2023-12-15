<?php

namespace App\Workplaces;

use App\Resources\MineResources\Coal;
use App\Resources\MineResources\Iron;

class Mine extends BaseWorkplace
{
    protected int $totalResourcesAmount = 10000;
    protected int $currentResourceAmount = 10000;
    protected string $workType = 'Mining';
    protected string $resourcesType = 'Fossil';
    protected array $resourcesCollection = [Coal::class, Iron::class];
}