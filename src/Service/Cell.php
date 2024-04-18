<?php

namespace App\Service;

class Cell
{
    private int $posX;
    private int $posY;
    private array $neighbors;
    private ?Organism $actual = null;

    public function getPosX(): int
    {
        return $this->posX;
    }

    public function setPosX(int $posX): void
    {
        $this->posX = $posX;
    }

    public function getPosY(): int
    {
        return $this->posY;
    }

    public function setPosY(int $posY): void
    {
        $this->posY = $posY;
    }

    public function getNeighbors(): array
    {
        return $this->neighbors;
    }

    public function setNeighbors(array $neighbors): void
    {
        $this->neighbors = $neighbors;
    }

    public function getActual(): ?Organism
    {
        return $this->actual;
    }

    public function setActual(?Organism $actual): void
    {
        $this->actual = $actual;
    }
}
