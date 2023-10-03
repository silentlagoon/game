<?php

namespace App\Entities\Contracts;

use App\GameDate;
use App\Inventory\Inventory;
use App\Periods\Contracts\IPeriod;
use App\Position\EntityHitPointsOptions;
use App\Position\EntityMoveOptions;
use App\State\GameState;
use App\Tasks\Contracts\ITask;
use App\Tasks\TaskQueue;

interface IEntity
{
    public function digestPeriod(IPeriod $period, GameState $gameState): void;
    public function setDateOfDeath(GameDate $date);
    public function getDateOfDeath(): ?GameDate;
    public function getCurrentHitPoints(): int;
    public function setCurrentHitPoints(int $hitPoints): void;
    public function receiveDamage(int $hitPoints): int;
    public function regenerateDamage(int $hitPoints): int;
    public function getCurrentHitPointsPercent(): int;
    public function isDead(): bool;
    public function getName(): string;
    public function setName($name): void;
    public function getGoldIncomePerPeriod(): int;
    public function kill(GameDate $date);
    public function getCost(): int;
    public function canProduceNaturalResources(): bool;
    public function getInitialPositionX(): float;
    public function setInitialPositionX(float $initialPositionX): void;
    public function getInitialPositionY(): float;
    public function setInitialPositionY(float $initialPositionY): void;
    public function getEntitySpeed(): float;
    public function setEntitySpeed(float $entitySpeed): void;
    public function canMove(): bool;
    public function getMoveOptions(): ?EntityMoveOptions;
    public function setEntityMoveOptions(EntityMoveOptions $entityMoveOptions);
    public function getEntityHitPointsOptions(): EntityHitPointsOptions;
    public function setEntityHitPointsOptions(EntityHitPointsOptions $entityHitPointsOptions): void;
    public function move(): void;
    public function setTask(?ITask $task);
    public function getTask(): ?ITask;
    public function isSelected(): bool;
    public function setSelected(bool $value): void;
    public function getTaskQueue(): TaskQueue;
    public function setTaskQueue(TaskQueue $taskQueue);
    public function digestTasks();
    public function getInventory(): Inventory;
    public function setInventory(Inventory $inventory);
}