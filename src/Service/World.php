<?php

declare(strict_types=1);

namespace App\Service;

class World
{
    /**
     * @param Cell[] $organisms
     */
    public function __construct(
        private array $cells = [],
        private array $organisms = [],
        private int $squareLength = 0,
        private int $maxCycles = 0,
        private int $actualCycle = 0
    ) {
    }

    /**
     * @return Cell[]
     */
    public function getCells(): array
    {
        return $this->cells;
    }

    /**
     * @param Cell[] $cells
     */
    public function setCells(array $cells): void
    {
        $this->cells = $cells;
    }

    /**
     * @return Organism[]
     */
    public function getOrganisms(): array
    {
        return $this->organisms;
    }

    /**
     * @param Organism[] $organisms
     * @return void
     */
    public function setOrganisms(array $organisms): void
    {
        $this->organisms = $organisms;
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
