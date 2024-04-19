<?php

declare(strict_types=1);

namespace App\Service;

class WorldFactory
{
    public static function createWorld(
        $cells = [],
        $organisms = [],
        $squareLength = 10,
        $maxCycles = 10,
        $actualCycle = 0
    ): World
    {
        return new World($cells, $organisms, $squareLength, $maxCycles, $actualCycle);
    }
}
