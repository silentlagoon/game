<?php

namespace App\Entities\Contracts;

use App\GameDate;
<<<<<<< Updated upstream
=======
use App\Inventory\Equipment;
use App\Inventory\Inventory;
>>>>>>> Stashed changes
use App\Periods\Contracts\IPeriod;
use App\Position\EntityHitPointsOptions;
use App\Position\EntityMoveOptions;
use App\State\GameState;

interface IEntity
{
    public function digestPeriod(IPeriod $period, GameState $gameState): void;
    public function setDateOfDeath(GameDate $date);
    public function getDateOfDeath(): ?GameDate;
    public function getCurrentHitPoints(): int;
    public function setCurrgitentHitPoints(int $hitPoints): void;
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
    public function canUseEquipment(): bool;
    public function getEntityMoveOptions(): ?EntityMoveOptions;
    public function setEntityMoveOptions(EntityMoveOptions $entityMoveOptions);
    public function getEntityHitPointsOptions(): EntityHitPointsOptions;
    public function setEntityHitPointsOptions(EntityHitPointsOptions $entityHitPointsOptions): void;
<<<<<<< Updated upstream
=======
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
    public function getEquipment(): Equipment;
    public function setEquipment(Equipment $equipment): void;
    public function getResourceGatheredPerPeriod(): int;
>>>>>>> Stashed changes
}