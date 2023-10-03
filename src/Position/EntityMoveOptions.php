<?php

namespace App\Position;

use raylib\Texture;
use raylib\Vector2;

class EntityMoveOptions
{
    protected Vector2 $position;
    protected Vector2 $speed;
    protected ?Texture $texture = null;

    public function __construct(Vector2 $position, Vector2 $speed)
    {
        $this->position = $position;
        $this->speed = $speed;
    }

    /**
     * @return Vector2
     */
    public function getPosition(): Vector2
    {
        return $this->position;
    }

    /**
     * @param Vector2 $position
     */
    public function setPosition(Vector2 $position): void
    {
        $this->position = $position;
    }

    /**
     * @return Vector2
     */
    public function getSpeed(): Vector2
    {
        return $this->speed;
    }

    /**
     * @param Vector2 $speed
     */
    public function setSpeed(Vector2 $speed): void
    {
        $this->speed = $speed;
    }

    /**
     * @return Texture|null
     */
    public function getTexture(): ?Texture
    {
        return $this->texture;
    }

    public function setTexture(Texture $texture)
    {
        $this->texture = $texture;
    }
}