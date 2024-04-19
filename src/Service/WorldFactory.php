<?php

namespace App\Service;

class WorldFactory
{
    public function createWorld(): World
    {
        $cells = []; // Populate as needed
        $organisms = []; // Populate as needed
        $squareLength = 10;
        $maxCycles = 10;
        $actualCycle = 0;

        return new World($cells, $organisms, $squareLength, $maxCycles, $actualCycle);
    }
}
