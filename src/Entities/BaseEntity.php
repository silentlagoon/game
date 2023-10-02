<?php

namespace App\Entities;

use App\Game;
use App\GameDate;
use App\Entities\Contracts\IEntity;
use App\Exceptions\Profile\NotEnoughGoldToSpendException;
use App\NaturalResources\Contracts\INaturalResource;
use App\Periods\Contracts\IPeriod;
use App\Position\EntityHitPointsOptions;
use App\Position\EntityMoveOptions;
use App\State\GameState;
use App\Tasks\Contracts\ITask;
use App\Tasks\TaskQueue;
use App\Tasks\WalkTask;
use raylib\Rectangle;
use raylib\Vector2;

abstract class BaseEntity implements IEntity
{
    protected string $name;

    protected bool $canBeDamagedByNature;
    protected bool $canBeHealedByNature;
    protected bool $canCollapse;

    protected int $maxHitPoints;
    protected int $currentHitPoints;

    protected int $entityCost = 0;
    protected int $goldIncomePerPeriod = 0;

    protected bool $canProduceNaturalResources = false;
    protected array $produceNaturalResourcesCollection = [];

    protected bool $shouldConsumeFood = false;
    protected int $consumeFoodAmount = 0;
    protected int $hungerDamage = 0;

    protected bool $isMovable = false;
    protected float $initialPositionX = Game::SCREEN_WIDTH / 2;
    protected float $initialPositionY = Game::SCREEN_HEIGHT / 2;
    protected float $entitySpeed = 0.0;

    protected EntityMoveOptions $entityMoveOptions;
    protected EntityHitPointsOptions $entityHitPointsOptions;

    protected GameDate $dateOfDeath;
    protected GameState $gameState;

    protected ?ITask $task = null;
    protected TaskQueue $taskQueue;
    protected bool $isSelected = false;


    /**
     * @param GameState $gameState
     * @param bool $isFreeOfCost
     * @throws NotEnoughGoldToSpendException
     */
    public function __construct(GameState $gameState, bool $isFreeOfCost = false)
    {
        $this->gameState = $gameState;

        if (!$isFreeOfCost) {
            $this->gameState->spendGoldAmount($this->getCost());
        }
    }

    public function __toString()
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    /**
     * @param IPeriod $period
     * @param GameState $gameState
     * @return void
     */
    public function digestPeriod(IPeriod $period, GameState $gameState): void
    {
        $this->processNatureDamage($period);
        $this->processCollapseDamage($period);
        $earnings = $this->processIncome($period);

        if ($earnings > 0) {
            $gameState->addGoldAmount($earnings);
        }

        if ($this->canProduceNaturalResources()) {
            $this->produceNaturalResources();
        }

        if ($this->shouldConsumeFood()) {
            $this->processFoodConsuming();
        }
    }

    public function digestTasks()
    {
        if (!$this->getTask()) {
            if ($this->getTaskQueue()->getTasks()) {
                $this->setTask($this->getTaskQueue()->getNext());
                return $this->getTask()->handle($this);
            }
            return $this->move();
        }

        return $this->getTask()->handle($this);
    }

    public function getTaskQueue(): TaskQueue
    {
        return $this->taskQueue;
    }

    public function setTaskQueue(TaskQueue $taskQueue)
    {
        $this->taskQueue = $taskQueue;
    }

    public function isSelected(): bool
    {
        return $this->isSelected;
    }

    public function setSelected(bool $value): void
    {
        $this->isSelected = $value;
    }

    /**
     * @return EntityHitPointsOptions
     */
    public function getEntityHitPointsOptions(): EntityHitPointsOptions
    {
        return $this->entityHitPointsOptions;
    }

    /**
     * @param EntityHitPointsOptions $entityHitPointsOptions
     */
    public function setEntityHitPointsOptions(EntityHitPointsOptions $entityHitPointsOptions): void
    {
        $this->entityHitPointsOptions = $entityHitPointsOptions;
    }

    /**
     * @return EntityMoveOptions
     */
    public function getMoveOptions(): EntityMoveOptions
    {
        return $this->entityMoveOptions;
    }

    public function setEntityMoveOptions(EntityMoveOptions $entityMoveOptions)
    {
        $this->entityMoveOptions = $entityMoveOptions;
    }

    /**
     * @return float
     */
    public function getInitialPositionX(): float
    {
        return $this->initialPositionX;
    }

    /**
     * @param float $initialPositionX
     */
    public function setInitialPositionX(float $initialPositionX): void
    {
        $this->initialPositionX = $initialPositionX;
    }

    /**
     * @return float
     */
    public function getInitialPositionY(): float
    {
        return $this->initialPositionY;
    }

    /**
     * @param float $initialPositionY
     */
    public function setInitialPositionY(float $initialPositionY): void
    {
        $this->initialPositionY = $initialPositionY;
    }

    /**
     * @return float
     */
    public function getEntitySpeed(): float
    {
        return $this->entitySpeed;
    }

    /**
     * @param float $entitySpeed
     */
    public function setEntitySpeed(float $entitySpeed): void
    {
        $this->entitySpeed = $entitySpeed;
    }

    public function canMove(): bool
    {
        return $this->isMovable;
    }

    public function move(): void
    {
        if (!$this->canMove()) {
            return;
        }

        $moveOptions = $this->getMoveOptions();
        $hitPointsOptions = $this->getEntityHitPointsOptions();

        $entityPositionX = $moveOptions->getPosition()->x + $moveOptions->getSpeed()->x;
        $entityPositionY = $moveOptions->getPosition()->y + $moveOptions->getSpeed()->y;

        $moveOptions->setPosition(new Vector2($entityPositionX, $entityPositionY));
        $hitPointsBar = $hitPointsOptions->getBar();


        $hitPointsOptions->setBar(new Rectangle(
            $entityPositionX + 5,
            $entityPositionY - 7,
            $hitPointsBar->width,
            $hitPointsBar->height
        ));

        //Change movement direction if we hit borders
        if (
            ($moveOptions->getPosition()->x >= (GetScreenWidth() - $moveOptions->getRadius())) ||
            ($moveOptions->getPosition()->y <= $moveOptions->getRadius())
        ) {
            $entitySpeedX = $moveOptions->getSpeed()->x *= -1.0;
            $entitySpeedY = $moveOptions->getSpeed()->y;

            $moveOptions->setSpeed(new Vector2($entitySpeedX, $entitySpeedY));
        }

        if (
            ($moveOptions->getPosition()->y >= (GetScreenHeight() - $moveOptions->getRadius())) ||
            ($moveOptions->getPosition()->y <= $moveOptions->getRadius())
        ) {
            $entitySpeedX = $moveOptions->getSpeed()->x;
            $entitySpeedY = $moveOptions->getSpeed()->y *= -1.0;

            $this->getMoveOptions()->setSpeed(new Vector2($entitySpeedX, $entitySpeedY));
        }
    }

    public function setTask(?ITask $task)
    {
        $this->task = $task;
    }

    public function getTask(): ?ITask
    {
        return $this->task;
    }

    public function getHungerDamage(): int
    {
        return $this->hungerDamage;
    }

    public function getConsumeFoodAmount(): int
    {
        return  $this->consumeFoodAmount;
    }

    public function shouldConsumeFood(): bool
    {
        return $this->shouldConsumeFood;
    }

    public function produceNaturalResources()
    {
        foreach ($this->getProduceNaturalResourcesCollection() as $resource) {
            /** @var INaturalResource $resource */
            $resource = new $resource();
            $totalQuantityProduced = $resource->getProducedQuantity();
            $gameStateNaturalResources = $this->gameState->getGameStateNaturalResources();
            $resourceAvailableQuantity = $gameStateNaturalResources->getObject($resource->getName()) ?? 0;

            if ($resourceAvailableQuantity) {
                $totalQuantityProduced += $resourceAvailableQuantity;
            }

            $this->gameState->getGameStateNaturalResources()
                ->addObject($totalQuantityProduced, $resource->getName());
        }
    }

    public function canProduceNaturalResources(): bool
    {
        return $this->canProduceNaturalResources;
    }

    /**
     * @return array
     */
    public function getProduceNaturalResourcesCollection(): array
    {
        return $this->produceNaturalResourcesCollection;
    }

    public function getCost(): int
    {
        return $this->entityCost;
    }

    /**
     * @param GameDate $date
     * @return void
     */
    public function setDateOfDeath(GameDate $date)
    {
        $this->dateOfDeath = $date;
    }

    /**
     * @return GameDate|null
     */
    public function getDateOfDeath(): ?GameDate
    {
        return $this->dateOfDeath ?? null;
    }

    public function getCurrentHitPoints(): int
    {
        return $this->currentHitPoints;
    }

    public function getMaxHitPoints(): int
    {
        return $this->maxHitPoints;
    }

    public function setCurrentHitPoints(int $hitPoints): void
    {
        $this->currentHitPoints = $hitPoints;
    }

    public function receiveDamage(int $hitPoints): int
    {
        $resultHitPointValue = $this->currentHitPoints - $hitPoints;
        return $this->currentHitPoints = max($resultHitPointValue, 0);
    }

    public function regenerateDamage(int $hitPoints): int
    {
        $resultHitPointValue = $this->currentHitPoints + $hitPoints;
        return $this->currentHitPoints = min($resultHitPointValue, $this->maxHitPoints);
    }

    public function getCurrentHitPointsPercent(): int
    {
        if ($this->getCurrentHitPoints() > 0) {
            return (int) (($this->getCurrentHitPoints() / $this->getMaxHitPoints()) * 100);
        }
        return 0;
    }

    public function isDead(): bool
    {
        return $this->currentHitPoints === 0;
    }

    public function getName(): string
    {
        return $this->name ?? (new \ReflectionClass($this))->getShortName();
    }

    public function setName($name): void
    {
        $this->name = ucfirst($name);
    }

    public function getGoldIncomePerPeriod(): int
    {
        return $this->isDead() ? 0 : $this->goldIncomePerPeriod;
    }

    /**
     * @param GameDate $date
     * @return void
     */
    public function kill(GameDate $date)
    {
        $this->setCurrentHitPoints(0);
        $this->dateOfDeath = $date;
    }

    /**
     * @param IPeriod $period
     * @return array
     */
    protected function processCollapseDamage(IPeriod $period): array
    {
        $damageReceived = $period->getCollapseDamage();

        if ($this->canCollapse) {
            $this->receiveDamage($period->getCollapseDamage());
        }

        $resultHitPoints = sprintf('%s / %s', $this->getCurrentHitPoints(), $this->getMaxHitPoints());
        return compact('damageReceived', 'resultHitPoints');
    }

    protected function processFoodConsuming()
    {
        $totalFoodValueAvailable = $this->gameState->getGameStateNaturalResources()->getTotalFoodValue();
        $foodToConsume = $this->getConsumeFoodAmount();

        if ($totalFoodValueAvailable < $foodToConsume) {
            $this->receiveDamage($this->getHungerDamage());
        }

        $consumables = $this->gameState->getGameStateNaturalResources()->getObjects();

        foreach ($consumables as $key => $value) {
            if ($foodToConsume === 0) {
                break;
            }

            if ($foodToConsume -= $value !== 0) {
                $this->gameState->getGameStateNaturalResources()
                    ->addObject(0, $key);
                continue;
            }

            $value = $value - $foodToConsume;
            $this->gameState->getGameStateNaturalResources()
                ->addObject($value, $key);
        }
    }

    /**
     * @param IPeriod $period
     * @return array
     */
    protected function processNatureDamage(IPeriod $period): array
    {
        $damageReceived = $period->getNatureDamage();
        $damageHealed = $period->getNatureHealing();

        if ($this->canBeDamagedByNature) {
            $this->receiveDamage($period->getNatureDamage());
        }

        if ($this->canBeHealedByNature) {
            $this->regenerateDamage($period->getNatureHealing());
        }

        $resultHitPoints = sprintf('%s / %s', $this->getCurrentHitPoints(), $this->getMaxHitPoints());

        return compact('damageReceived', 'damageHealed', 'resultHitPoints');
    }

    /**
     * @param IPeriod $period
     * @return int
     */
    protected function processIncome(IPeriod $period): int
    {
        $totalIncome = 0;

        if ($this->goldIncomePerPeriod > 0) {
            $totalIncome += $this->goldIncomePerPeriod;
        }

        return $totalIncome;
    }
}