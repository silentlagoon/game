<?php

namespace App\Resources\Contracts;

interface IResources
{
    public function getName(): string;
    public function gitgetResourceTypeName(): string;
    public function getSellCost(): int;

}