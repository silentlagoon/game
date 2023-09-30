<?php

namespace App\Periods\Contracts;

interface IPeriod
{
    public function getNatureHealing(): int;
    public function getNatureDamage(): int;
    public function getCollapseDamage(): int;
    public function getName(): string;
    public function getDurationInDays(): int;
}