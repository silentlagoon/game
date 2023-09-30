<?php

namespace App\State;

class GameStateObjects
{
    protected array $objects = [];

    public function getObjects(): array
    {
        return $this->objects;
    }

    /**
     * @param $object
     * @param $key
     * @return void
     */
    public function addObject($object, $key): void
    {
        $this->objects[$key] = $object;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function getObject($key): mixed
    {
        return $this->objects[$key] ?? null;
    }
}