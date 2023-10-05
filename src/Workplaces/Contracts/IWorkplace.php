<?php

namespace App\Workplaces\Contracts;

use App\Entities\Contracts\IEntity;
use App\Periods\Contracts\IPeriod;

interface IWorkplace
{
    public function digestPeriod(IPeriod $period): void;
    public function getWorkers(): array;
    public function addWorker(IEntity $worker): void;
    public function removeWorkers(): void;
    public function isEmpty(): bool;
}