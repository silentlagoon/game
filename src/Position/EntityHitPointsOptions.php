<?php

namespace App\Position;

use raylib\Rectangle;

class EntityHitPointsOptions
{
    protected Rectangle $bar;

    public function __construct(Rectangle $bar)
    {
        $this->bar = $bar;
    }

    /**
     * @return Rectangle
     */
    public function getBar(): Rectangle
    {
        return $this->bar;
    }

    /**
     * @param Rectangle $bar
     */
    public function setBar(Rectangle $bar): void
    {
        $this->bar = $bar;
    }


}