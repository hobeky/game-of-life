<?php

declare(strict_types=1);

namespace App\Service;

class OrganismFactory
{
    public function createFromArray(array $organismsData): array
    {
        $organisms = [];
        foreach ($organismsData as $data) {
            $organisms[] = new Organism(
                $data['species'],
                $data['x_pos'],
                $data['y_pos']
            );
        }

        return $organisms;
    }
}
