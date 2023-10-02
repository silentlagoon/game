<?php

namespace App;

use App\Entities\Contracts\IEntity;
use raylib\Texture;

class GameTextures
{
    const ENTITY_STATE_ALIVE = 'alive';
    const ENTITY_STATE_DEAD = 'dead';

    protected array $textures;

    public function __construct(array $images)
    {
        foreach ($images as $imageType => $states) {
            foreach ($states as $stateName => $state) {
                $currentImage = LoadImage($state['path']);

                if (isset($state['resize'])) {
                    ImageResize($currentImage, ...$state['resize']);
                }

                $this->textures[$imageType][$stateName] = LoadTextureFromImage($currentImage);
                UnloadImage($currentImage);
            }
        }
    }

    public function unload()
    {
        foreach ($this->textures as $textureType => $states) {
            foreach ($states as $state) {
                UnloadTexture($state);
            }
        }
    }

    public function getEntityTexture(IEntity $entity): Texture
    {
        $entityName = (new \ReflectionClass($entity))->getShortName();
        $entityTextures = $this->textures[strtolower($entityName)];

        return $entity->isDead() ? $entityTextures[static::ENTITY_STATE_DEAD] : $entityTextures[static::ENTITY_STATE_ALIVE];
    }
}