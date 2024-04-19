<?php

namespace App\Service;

class World
{
    /**
     * @var Cell[]
     */
    private array $cells;

    /**
     * @var Organism[]
     */
    private array $organisms;
    private int $squareLength;
    private int $maxCycles;
    private int $actualCycle;


    /**
     * @param array $cells
     * @param Cell[] $organisms
     * @param int $squareLength
     * @param int $maxCycles
     * @param int $actualCycle
     */
    public function __construct(
        ?array $cells = [],
        ?array $organisms = [],
        ?int $squareLength = 0,
        ?int $maxCycles = 0,
        ?int $actualCycle = 0
    )
    {
        $this->cells = $cells;
        $this->organisms = $organisms;
        $this->squareLength = $squareLength;
        $this->maxCycles = $maxCycles;
        $this->actualCycle = $actualCycle;
    }

    /**
     * @return Cell[]
     */
    public function getCells(): array
    {
        return $this->cells;
    }

    /**
     * @param Cell[] $cell
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
