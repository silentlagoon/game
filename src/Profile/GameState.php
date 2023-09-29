<?php

namespace App\Profile;

use App\Entities\Living\Humans\Worker;
use App\Exceptions\Profile\NotEnoughGoldToSpendException;

class GameState
{
    const STARTING_GOLD_AMOUNT = 100;

    protected string $userName;
    protected string $settlementName;

    protected bool $isUserNameBeenSet = false;
    protected bool $isPaused = false;

    protected int $totalWorkersOwned = 0;
    protected int $totalHousesOwned = 0;

    protected int $currentGoldAmount = 0;

    protected array $workersGraveyard = [];

    public function setUserName($userName): GameState
    {
        $this->userName = $userName;
        return $this;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getSettlementName(): string
    {
        return $this->settlementName;
    }

    public function setSettlementName(string $settlementName): GameState
    {
        $this->settlementName = $settlementName;
        return $this;
    }

    public function spendGoldAmount(int $amountToSpend): int
    {
        $resultGoldAmount = $this->currentGoldAmount - $amountToSpend;

        if ($resultGoldAmount < 0) {
            throw new NotEnoughGoldToSpendException();
        }

        return $this->currentGoldAmount = $resultGoldAmount;
    }

    public function addGoldAmount(int $amountToAdd): int
    {
        return $this->currentGoldAmount += $amountToAdd;
    }

    public function addStartingGoldAmount(): int
    {
        return $this->addGoldAmount(static::STARTING_GOLD_AMOUNT);
    }

    public function getCurrentGoldAmount(): int
    {
        return $this->currentGoldAmount;
    }

    public function getTotalWorkersOwned(): int
    {
        return $this->totalWorkersOwned;
    }

    public function incrementTotalWorkersOwned(int $increment = 1)
    {
        $this->totalWorkersOwned += $increment;
    }

    public function getTotalHousesOwned(): int
    {
        return $this->totalHousesOwned;
    }

    public function incrementTotalHousesOwned(int $increment = 1)
    {
        $this->totalHousesOwned += $increment;
    }

    public function getDeadWorkers(): array
    {
        return $this->workersGraveyard;
    }

    public function addDeadWorkerToGraveyard(Worker $worker): void
    {
        $this->workersGraveyard[] = $worker;
    }

    /**
     * @return bool
     */
    public function isUserNameBeenSet(): bool
    {
        return $this->isUserNameBeenSet;
    }

    /**
     * @param bool $hasUserNameBeenSet
     */
    public function setIsUserNameBeenSet(bool $hasUserNameBeenSet): void
    {
        $this->isUserNameBeenSet = $hasUserNameBeenSet;
    }

    public function decrementTotalWorkersOwned()
    {
        $this->totalWorkersOwned = max($this->totalWorkersOwned - 1, 0);
    }

    /**
     * @return bool
     */
    public function isPaused(): bool
    {
        return $this->isPaused;
    }

    public function pauseGame()
    {
        $this->isPaused = true;
    }

    public function continueGame()
    {
        $this->isPaused = false;
    }
}