<?php

namespace App\Entities\Structures;

use App\Entities\BaseEntity;

class BaseStructureEntity extends BaseEntity
{
    protected bool $canBeDamagedByNature = false;
    protected bool $canBeHealedByNature = false;
    protected bool $canCollapse = true;
}