<?php

namespace App;

use App\Entities\EntitiesFactory;
use App\Exceptions\Profile\NotEnoughGoldToSpendException;
use App\Profile\Profile;

class Game
{
    protected Profile $profile;
    protected Digestor $digestor;
    protected EntitiesFactory $entitiesFactory;

    public function __construct(Profile $profile, Digestor $digestor, EntitiesFactory $entitiesFactory)
    {
        $this->profile = $profile;
        $this->digestor = $digestor;
        $this->entitiesFactory = $entitiesFactory;
    }

    public function start()
    {
        while (true) {

            echo sprintf('%s has started!', $this->digestor->getTimesOfYear()->getCurrentPeriod()->getName());
            echo "\r\n";
            echo sprintf('Your current balance is: %d gold', $this->profile->getCurrentGoldAmount());
            echo "\r\n";
            echo sprintf('You have %d workers and %d houses', $this->profile->getTotalWorkersOwned(), $this->profile->getTotalHousesOwned());
            echo "\r\n";
            readline('Hit any key to continue...');
            echo "\r\n";

            $workersToHire = (int) readline('How many workers you want to hire? ');
            $housesToBuild = (int) readline('How many houses you want to build? ');

            if ($workersToHire) {
                for ($i = 1; $i <= $workersToHire; $i++) {

                    $workerName = readline(sprintf('Do you want to name worker number %d ? ', $i));

                    try {
                        $this->digestor->addEntity($this->entitiesFactory->createWorker($workerName, $this->profile));
                        $this->profile->incrementTotalWorkersOwned();
                    } catch (NotEnoughGoldToSpendException $e) {
                        echo sprintf('Your current balance is: %d gold, you cannot afford to buy more workers', $this->profile->getCurrentGoldAmount());
                        echo "\r\n";
                    }
                }
            }

            if ($housesToBuild) {
                for ($i = 1; $i <= $housesToBuild; $i++) {

                    try {
                        $this->digestor->addEntity($this->entitiesFactory->createSmallHouse($this->profile));
                        $this->profile->incrementTotalHousesOwned();
                    } catch (NotEnoughGoldToSpendException $e) {
                        echo sprintf('Your current balance is: %d gold, you cannot afford to buy more houses', $this->profile->getCurrentGoldAmount());
                        echo "\r\n";
                    }
                }
            }

            $this->digestor->digestEntities($this->profile);

            if (!empty($graveyard = $this->profile->getDeadWorkers())) {
                echo 'You have dead workers';
                echo "\r\n";
            }
        }
    }
}