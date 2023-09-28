<?php

namespace App\Entities;

use App\Entities\Living\Humans\Worker;
use App\Entities\Structures\SmallHouse;
use App\Exceptions\Profile\NotEnoughGoldToSpendException;
use App\Profile\Profile;

class EntitiesFactory
{
    /**
     * @param string $workerName
     * @param Profile $profile
     * @return Worker
     * @throws NotEnoughGoldToSpendException
     */
    public function createWorker(string $workerName, Profile $profile): Worker
    {
        $worker = new Worker($profile);

        if (!empty($workerName)) {
            $worker->setName($workerName);
        }

        return $worker;
    }

    /**
     * @param Profile $profile
     * @return SmallHouse
     * @throws NotEnoughGoldToSpendException
     */
    public function createSmallHouse(Profile $profile): SmallHouse
    {
        return new SmallHouse($profile);
    }
}