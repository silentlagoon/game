<?php

namespace App\Tasks\Contracts;

use App\Entities\Contracts\IEntity;

interface ITask
{
    public function handle(IEntity $entity);
}