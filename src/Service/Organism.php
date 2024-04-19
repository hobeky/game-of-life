<?php

declare(strict_types=1);

namespace App\Service;

class Organism extends AbstractOrganism implements OrganismInterface
{
    public function __construct(
        private string $name,
        int $posX,
        int $posY
    ) {
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
