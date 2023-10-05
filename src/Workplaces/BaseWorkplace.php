<?php

namespace App\Workplaces;

use App\Entities\Contracts\IEntity;
use App\Periods\Contracts\IPeriod;
use App\State\GameState;
use App\Workplaces\Contracts\IWorkplace;

abstract class BaseWorkplace implements IWorkplace
{
    protected GameState $gameState;
    /** @var $workers IEntity[] */
    protected array $workers = [];
    protected int $totalResourcesAmount = 0;
    protected int $currentResourceAmount = 0;

    public function __construct(GameState $gameState)
    {
        $this->gameState = $gameState;
    }

    public function digestPeriod(IPeriod $period): void
    {
        $gatheredResources = 0;

        foreach ($this->workers as $key => $worker) {
            if ($this->isEmpty()) {
                break;
            }

            if ($this->currentResourceAmount <= $worker->getResourceGatheredPerPeriod()) {
                $gatheredResources += $this->currentResourceAmount;
                $this->currentResourceAmount = 0;
                break;
            }

            $gatheredResources += $worker->getResourceGatheredPerPeriod();
            $this->currentResourceAmount -= $gatheredResources;

            //Our workers are now off from main digest so we have to digest them here
            //TODO:: Digestor should be able to handle this type of case
            //TODO:: Not to duplicate entities digest code here
            $worker->digestPeriod($period, $this->gameState);

            if ($worker->isDead()) {
                unset($this->workers[$key]);
            }
        }

        $this->gameState->addCurrentResourcesGathered($gatheredResources);
    }

    public function getWorkers(): array
    {
        return $this->workers;
    }

    public function addWorker(IEntity $worker): void
    {
        $this->workers[] = $worker;
    }

    public function removeWorkers(): void
    {
        $this->workers = [];
    }

    public function isEmpty(): bool
    {
        return $this->currentResourceAmount === 0;
    }
}