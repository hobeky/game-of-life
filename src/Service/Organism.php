<?php

namespace App\Service;

class Organism extends AbstractOrganism implements OrganismInterface
{
    private string $name;

    public function __construct(string $name, int $posX, int $posY)
    {
        $this->name = $name;
        $this->posX = $posX;
        $this->posY = $posY;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
