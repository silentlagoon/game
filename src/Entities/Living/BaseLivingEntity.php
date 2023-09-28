<?php

namespace App\Entities\Living;

use App\Entities\BaseEntity;

class BaseLivingEntity extends BaseEntity
{
    protected bool $canBeDamagedByNature = true;
    protected bool $canBeHealedByNature = true;
    protected bool $canCollapse = false;
}