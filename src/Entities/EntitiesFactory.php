<?php

namespace App\Entities;

use App\Entities\Living\Humans\Worker;
use App\Entities\Structures\SmallHouse;
use App\Exceptions\Profile\NotEnoughGoldToSpendException;
use App\Profile\GameState;

class EntitiesFactory
{
    /**
     * @param string $workerName
     * @param GameState $profile
     * @return Worker
     * @throws NotEnoughGoldToSpendException
     */
    public function createWorker(string $workerName, GameState $profile): Worker
    {
        $worker = new Worker($profile);

        if (!empty($workerName)) {
            $worker->setName($workerName);
        }

        return $worker;
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