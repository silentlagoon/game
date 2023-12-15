<?php

namespace App\Workplaces;

use App\Entities\Contracts\IEntity;
use App\Inventory\Items\Contracts\IGiveGoldIncome;
use App\Inventory\Items\Tools\PickAxe;
use App\Periods\Contracts\IPeriod;
use App\Resources\Contracts\IResources;
use App\State\GameState;
use App\Workplaces\Contracts\IWorkplace;

abstract class BaseWorkplace implements IWorkplace
{
    protected GameState $gameState;
    /** @var $workers IEntity[] */
    protected array $workers = [];
    protected int $totalResourcesAmount = 0;
    protected int $currentResourceAmount = 0;
    protected array $resourcesCollection = [];

    protected string $workType;
    protected string $resourcesType;

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

            $worker->getEquipment()->getItemByType($this->workType);

            $resourceModifier = $worker->modifyResourceGatheredPerPeriod();
            $gatheredResources += $worker->getResourceGatheredPerPeriod() +
                $worker->modifyResourceGatheredPerPeriod();

            $this->processResources($resourceModifier,$gatheredResources);
            $worker->processWorkWithTool($period);

//            $this->currentResourceAmount = $this->totalResourcesAmount -
//                array_sum($this->gameState->getGameStateResources()
//                    ->getObjects());
            dump($this->gameState->getGameStateResources());
            dump($this->currentResourceAmount);
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

    public function processResources($resourceModifier,$gatheredResources): void
    {

        foreach ($this->getResourcesCollection() as $resource) {
            /** @var IResources $resource */
            $resource = new $resource;
            $resourcesPerTick = mt_rand(0 , $gatheredResources);
            $gatheredResources = $gatheredResources - $resourcesPerTick;
            $resultResources =  $resourcesPerTick + $resourceModifier;
            $gameStateResources = $this->gameState->getGameStateResources();
            $resourceAvailableQuantity = $gameStateResources->getObject($resource->getName()) ?? 0;

            if ($resourceAvailableQuantity) {
                $resultResources += $resourceAvailableQuantity;
            }

            $this->currentResourceAmount = $this->totalResourcesAmount - $resultResources;
            $this->gameState->getGameStateResources()->addObject($resultResources, $resource->getName());

        }
        $this->currentResourceAmount = $this->totalResourcesAmount -
            array_sum($this->gameState->getGameStateResources()
                ->getObjects());
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

    public function getResourcesCollection(): array
    {
        return $this->resourcesCollection;
    }

    public function getResourcesType(): string
    {
        return $this->resourcesType;
    }
}