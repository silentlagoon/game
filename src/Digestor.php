<?php

namespace App;

use App\Entities\Contracts\IEntity;
use App\Entities\Living\BaseLivingEntity;
use App\Entities\Living\Humans\Worker;
use App\Periods\TimesOfYear;
use App\Profile\Profile;

class Digestor
{
    protected array $entities;
    protected TimesOfYear $timesOfYear;
    protected int $currentYear;

    public function __construct(array $entities, TimesOfYear $timesOfYear, int $currentYear)
    {
        $this->entities = $entities;
        $this->timesOfYear = $timesOfYear;
        $this->currentYear = $currentYear;
    }

    public function digestEntities(Profile $profile)
    {
        foreach ($this->entities as $key => $entity) {

            if ($entity->isDead()) {
                if ($entity instanceof Worker) {
                    $profile->addDeadWorkerToGraveyard($entity);
                }

                unset($this->entities[$key]);
                continue;
            }

            $entity->digestPeriod($this->timesOfYear->getCurrentPeriod(), $profile);
        }

        $this->timesOfYear->moveToNextPeriod();
        $this->currentYear++;
    }

    public function addEntity(IEntity $entity): void
    {
        $this->entities[] = $entity;
    }

    public function getTimesOfYear(): TimesOfYear
    {
        return $this->timesOfYear;
    }
}