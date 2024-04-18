<?php

namespace App\Service;

class World
{
    /**
     * @var Cell[]
     */
    private array $cell;
    private int $squareLength;
    private int $maxCycles;
    private int $actualCycle;

    /**
     * @return Cell[]
     */
    public function getCell(): array
    {
        return $this->cell;
    }

    /**
     * @param Cell[] $cell
     */
    public function setCell(array $cell): void
    {
        $this->cell = $cell;
    }

    public function getSquareLength(): int
    {
        return $this->squareLength;
    }

    public function setSquareLength(int $squareLength): void
    {
        $this->squareLength = $squareLength;
    }

    public function getMaxCycles(): int
    {
        return $this->maxCycles;
    }

    public function setMaxCycles(int $maxCycles): void
    {
        $this->maxCycles = $maxCycles;
    }

    public function getActualCycle(): int
    {
        return $this->actualCycle;
    }

    public function setActualCycle(int $actualCycle): void
    {
        $this->actualCycle = $actualCycle;
    }
}
