<?php

namespace App\Entities;

use App\Entities\Living\Humans\Worker;
use App\Entities\Structures\SmallHouse;
use App\Entities\Living\Animals\Cow;
use App\Exceptions\Profile\NotEnoughGoldToSpendException;
use App\State\GameState;
use Nubs\RandomNameGenerator\All;

class EntitiesFactory
{
    /**
     * @param GameState $profile
     * @param int|null $currentHitPoints
     * @param string|null $workerName
     * @return Worker
     * @throws NotEnoughGoldToSpendException
     */
    public function createWorker(GameState $profile, ?int $currentHitPoints = null, string $workerName = null): Worker
    {
        $worker = new Worker($profile);

        if (!is_null($currentHitPoints)) {
            $worker->setCurrentHitPoints($currentHitPoints);
        }

        if (!empty($workerName)) {
            $worker->setName($workerName);
        } else {
            $namesGenerator = All::create();
            $worker->setName($namesGenerator->getName());
        }

        return $worker;
    }

    /**
     * @param GameState $profile
     * @return Cow
     * @throws NotEnoughGoldToSpendException
     */
    public function createCow(GameState $profile): Cow
    {
        return new Cow($profile);
    }
    /**
     * @param GameState $profile
     * @return SmallHouse
     * @throws NotEnoughGoldToSpendException
     */
    public function createSmallHouse(GameState $profile): SmallHouse
    {
        return new SmallHouse($profile);
    }
}