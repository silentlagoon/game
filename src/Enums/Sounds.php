<?php

namespace App\Enums;

use MyCLabs\Enum\Enum;

final class Sounds extends Enum
{
    private const INTRO = 'intro';
    private const KEYBOARD_SOUND = 'keyboard_sound';

    public static function INTRO(): Sounds
    {
        return new Sounds(self::INTRO);
    }

    public static function KEYBOARD_SOUND(): Sounds
    {
        return new Sounds(self::KEYBOARD_SOUND);
    }
}