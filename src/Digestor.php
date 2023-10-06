<?php

namespace App;

use App\Entities\Contracts\IEntity;
use App\Entities\Living\Humans\Contracts\IPopulation;
use App\Entities\Living\Humans\Worker;
use App\Entities\Living\Animals\Cow;
use App\Entities\Structures\SmallHouse;
use App\Periods\TimesOfYear;
use App\State\GameState;
use App\Workplaces\Contracts\IWorkplace;
use Illuminate\Support\Collection;

class Digestor
{
    protected Collection $entities;
    protected Collection $workplaces;
    protected TimesOfYear $timesOfYear;
    protected GameState $gameState;

    public function __construct(
        GameState $gameState,
        Collection $entities,
        Collection $workplaces,
        TimesOfYear $timesOfYear
    )
    {
        $this->gameState = $gameState;
        $this->entities = $entities;
        $this->workplaces = $workplaces;
        $this->timesOfYear = $timesOfYear;
    }

    public function digestEntities()
    {
        foreach ($this->entities as $key => $entity) {
            $entity->digestPeriod($this->timesOfYear->getCurrentPeriod(), $this->gameState);

            if ($entity->isDead()) {
                $this->removeEntityFromDigest($entity, $key);
            }
        }
    }

    public function digestWorkplaces()
    {
        /** @var IWorkplace $workplace */
        foreach ($this->workplaces as $workplace) {
            $workplace->digestPeriod($this->timesOfYear->getCurrentPeriod());

            if ($workplace->isEmpty()) {
                $this->removeEntitiesFromWorkplace($workplace);
            }
        }
    }

    public function digestEntitiesTasks()
    {
        foreach ($this->entities as $entity) {
            $entity->digestTasks();
        }
    }

    public function getPopulation(): int
    {
        return $this->gameState->getPopulation();
    }

    public function addWorkplace(IWorkplace $workplace)
    {
        $this->workplaces->push($workplace);
    }

    public function getTimesOfYear(): TimesOfYear
    {
        return $this->timesOfYear;
    }

    public function getEntities(): Collection
    {
        return $this->entities;
    }

    public function getUniqueEntitiesNames(): Collection
    {
        return $this->entities->map(function (IEntity $entity) {
           return (new \ReflectionClass($entity))->getName();
        })->unique();
    }

    public function getEntitiesOfType(string $entityType): Collection
    {
        $workplaceWorkers = new Collection();

        if ($entityType === Worker::class) {
            /** @var IWorkplace $workplace */
            foreach ($this->workplaces as $workplace) {
                if ($anyWorkers = $workplace->getWorkers()) {
                    $workplaceWorkers = $workplaceWorkers->merge($anyWorkers);
                }
            }
        }

        $entities = $this->entities->filter(function (IEntity $entity) use ($entityType) {
            return $entity instanceof $entityType;
        })->values();

        return $entities->merge($workplaceWorkers);
    }

    public function addEntity(IEntity $entity): void
    {
        $this->entities->push($entity);

        if ($entity instanceof Worker) {
            $this->gameState->incrementTotalWorkersOwned();
            $this->gameState->incrementPopulation();
        }

        if ($entity instanceof Cow) {
            $this->gameState->incrementTotalCowsOwned();
        }

        if ($entity instanceof SmallHouse) {
            $this->gameState->incrementTotalHousesOwned();
        }
    }

    public function addEntityToWorkplace(IEntity $worker, IWorkplace $workplace): void
    {
        /** @var IEntity $entity */
        foreach ($this->entities as $key => $entity) {
            if ($entity->getName() === $worker->getName()) {
                $entity->setIsWorking(true);
                $workplace->addWorker($entity);
                $this->entities->forget($key);
            }
        }
    }

    protected function removeEntityFromDigest(IEntity $entity, int $key): void
    {
        if ($entity instanceof Worker) {
            $this->gameState->addDeadWorkerToGraveyard($entity);
            $this->gameState->decrementTotalWorkersOwned();
        }

        if ($entity instanceof Cow) {
            $this->gameState->decrementTotalCowsOwned();
        }

        if ($entity instanceof SmallHouse) {
            $this->gameState->incrementTotalHousesOwned(-1);
        }

        if ($entity instanceof IPopulation) {
            $this->gameState->incrementPopulation(-1);
        }

        $entity->setDateOfDeath(new GameDate(
            $this->getTimesOfYear()->getCurrentYear(),
            $this->timesOfYear->getCurrentPeriod(),
            $this->gameState->getDaysFromTicks()
        ));

        $this->entities->forget($key);
    }

    protected function removeEntitiesFromWorkplace(IWorkplace $workplace): void
    {
        /** @var IEntity $worker */
        foreach ($workplace->getWorkers() as $worker) {
            $worker->setIsWorking(false);
            $this->entities->push($worker);
        }

        $workplace->removeWorkers();
    }
}