<?php

declare(strict_types=1);

namespace App\Service;

class Cell
{
    private array $neighbors = [];

    public function __construct(
        private int $posX,
        private int $posY,
        private ?Organism $organism = null
    ) {
    }

    public function getNeighbors(): array
    {
        return $this->neighbors;
    }

    public function setNeighbors(array $neighbors): void
    {
        $this->neighbors = $neighbors;
    }

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

    public function getOrganism(): ?Organism
    {
        return $this->organism;
    }

    public function setOrganism(?Organism $organism): void
    {
        $this->organism = $organism;
    }
}
