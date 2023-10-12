<?php

namespace App\Inventory\Items\Contracts;

interface IGiveGoldIncome
{
    public function getIncomeModifier(): int;

}