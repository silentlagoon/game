<?php

namespace App\Inventory\Items\Contracts;

<<<<<<< Updated upstream
interface IGiveGoldIncome
{
    public function getIncomeModifier(): int;
=======
use App\Periods\Contracts\IPeriod;

interface IGiveGoldIncome
{
    public function getIncomeModifier(): int;
    public function getWorkType(): string;
    public function processUseDamage(IPeriod $period): array;
    public function setIsBeingUsed(bool $value): void;
    public function isBeingUsed(): bool;
    public function getModifier(): float;
    public function setModifier(float $modifier): void;
    public function isBroken(): bool;
>>>>>>> Stashed changes

}