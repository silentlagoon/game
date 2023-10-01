<?php

namespace App\State;

use App\Entities\Contracts\IEntity;
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
    protected int $totalCowsOwned = 0;
    protected int $totalHousesOwned = 0;

    protected int $currentGoldAmount = 0;

    protected array $workersGraveyard = [];

    protected int $ticks = 1;

    protected bool $error = false;
    protected string $errorMessage = '';
    protected float $currentErrorMessageTickDuration = 0.00;

    protected GameStateObjects $gameStateObjects;
    protected GameStateSounds $gameStateSounds;

    public function __construct(GameStateObjects $gameStateObjects, GameStateSounds $gameStateSounds)
    {
        $this->gameStateObjects = $gameStateObjects;
        $this->gameStateSounds = $gameStateSounds;
    }

    /**
     * @return float
     */
    public function getCurrentErrorMessageTickDuration(): float
    {
        return $this->currentErrorMessageTickDuration;
    }

    /**
     * @param float $currentErrorMessageTickDuration
     */
    public function setCurrentErrorMessageTickDuration(float $currentErrorMessageTickDuration): void
    {
        $this->currentErrorMessageTickDuration = $currentErrorMessageTickDuration;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @param string $errorMessage
     */
    public function setErrorMessage(string $errorMessage): void
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return bool
     */
    public function isError(): bool
    {
        return $this->error;
    }

    /**
     * @param bool $error
     */
    public function setError(bool $error): void
    {
        $this->error = $error;
    }

    /**
     * @return GameStateObjects
     */
    public function getGameStateObjects(): GameStateObjects
    {
        return $this->gameStateObjects;
    }

    /**
     * @return GameStateSounds
     */
    public function getGameStateSounds(): GameStateSounds
    {
        return $this->gameStateSounds;
    }

    /**
     * @return int
     */
    public function getTicks(): int
    {
        return $this->ticks;
    }

    /**
     * @param int $ticks
     */
    public function setTicks(int $ticks): void
    {
        $this->ticks = $ticks;
    }

    public function incrementTicks()
    {
        $this->ticks++;
    }

    public function getDaysFromTicks(): int
    {
        if ($this->ticks > 0) {
            $days = $this->ticks / 100;
            return $days > 0 ? (int) $days + 1 : 1;
        }
        return 1;
    }

    /**
     * @param $userName
     * @return GameState
     */
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

    /**
     * @param string $settlementName
     * @return GameState
     */
    public function setSettlementName(string $settlementName): GameState
    {
        $this->settlementName = $settlementName;
        return $this;
    }

    /**
     * @param int $amountToSpend
     * @return int
     * @throws NotEnoughGoldToSpendException
     */
    public function spendGoldAmount(int $amountToSpend): int
    {
        $resultGoldAmount = $this->currentGoldAmount - $amountToSpend;

        if ($resultGoldAmount < 0) {
            throw new NotEnoughGoldToSpendException('You dont have enough gold!');
        }

        return $this->currentGoldAmount = $resultGoldAmount;
    }

    /**
     * @param int $amountToAdd
     * @return int
     */
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

    /**
     * @param int $increment
     * @return void
     */
    public function incrementTotalWorkersOwned(int $increment = 1)
    {
        $this->totalWorkersOwned += $increment;
    }

    public function getTotalCowsOwned(): int
    {
        return $this->totalCowsOwned;
    }

    /**
     * @param int $increment
     * @return void
     */
    public function incrementTotalCowsOwned(int $increment = 1)
    {
        $this->totalCowsOwned += $increment;
    }

    public function getTotalHousesOwned(): int
    {
        return $this->totalHousesOwned;
    }

    /**
     * @param int $increment
     * @return void
     */
    public function incrementTotalHousesOwned(int $increment = 1)
    {
        $this->totalHousesOwned += $increment;
    }

    public function getDeadWorkers(): array
    {
        return $this->workersGraveyard;
    }

    /**
     * @param Worker $worker
     * @return void
     */
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

    public function decrementTotalCowsOwned()
    {
        $this->totalCowsOwned = max($this->totalCowsOwned - 1, 0);
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

    public function isEnoughGoldToBuy(IEntity $entity): bool
    {
        return $this->currentGoldAmount >= $entity->getCost();
    }
}