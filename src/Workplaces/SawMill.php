<?php

namespace App\Workplaces;

use App\Resources\WorldResources\Wood;

class SawMill extends BaseWorkplace
{
    protected int $totalResourcesAmount = 9000;
    protected int $currentResourceAmount = 9000;
    protected string $workType = 'Timbering';
    protected string $resourcesType = 'Wood';
    protected array $resourcesCollection = [Wood::class];

}