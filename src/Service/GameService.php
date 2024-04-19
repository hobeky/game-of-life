<?php

namespace App\Service;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GameService
{
    /**
     * @var World The world object that this service manages.
     */
    private World $world;

    private SymfonyStyle $io;

    public function __construct()
    {
        $this->world = (new WorldFactory())->createWorld();
    }

    public function getWorld(): World
    {
        return $this->world;
    }

    public function setWorldFromXmlFile(int $maxCycles, int $squareLength, array $organism)
    {
        $this->world->setMaxCycles($maxCycles);
        $this->world->setSquareLength($squareLength);
        $this->world->setOrganisms($organism);
    }

    public function createCellGrid(): void
    {
        $grid = [];
        for ($x = 0; $x < 10; $x++) {
            for ($y = 0; $y < 10; $y++) {
                $grid[$x][$y] = new Cell($x, $y);
            }
        }

        $this->world->setCells($grid);
    }

    public function initialiseOrganisms()
    {
        $cells = $this->world->getCells();
        $organisms = $this->world->getOrganisms();

        foreach ($organisms as $organism) {
            $x = $organism->getPosX();
            $y = $organism->getPosY();

            if (isset($cells[$x][$y])) {
                $cells[$x][$y]->setOrganism($organism);
            }
        }
    }

    public function runSimulation(InputInterface $input, OutputInterface $output)
    {

        $io = new SymfonyStyle($input, $output);
        $cycles = $this->world->getMaxCycles();
        for ($i = 0; $i < $cycles; $i++) {
            dump($this->world->getCells());
            $this->simulateIteration();  // Simulate one iteration
            dump($this->world->getCells());
            $grid = $this->world->getCells();  // Get the updated grid state after the iteration
            $dimension = $this->world->getSquareLength();  // Ensure dimension is still accurate
//            dump($grid);
            $this->outputGrid($grid, $dimension, $io);  // Output the current state of the grid
            $io->success($i);
            sleep(1);  // Wait for 1 second before the next iteration
        }
    }

    public function simulateIteration()
    {
        $dimension = $this->world->getSquareLength();
        $currentGrid = $this->world->getCells();
        $newGrid = array_fill(0, $dimension, array_fill(0, $dimension, null));

        for ($x = 0; $x < $dimension; $x++) {
            for ($y = 0; $y < $dimension; $y++) {
                $currentCell = $currentGrid[$x][$y];
                $currentOrganism = $currentCell->getOrganism();
                $neighbors = $currentCell->getNeighbors();
                $cellsByType = $this->groupCellsByOrganismType($neighbors);

                if ($currentOrganism !== null) {
                    $sameTypeCells = $cellsByType[$currentOrganism->getName()] ?? [];
                    if (count($sameTypeCells) == 2 || count($sameTypeCells) == 3) {
                        $newGrid[$x][$y] = new Cell($x, $y, clone $currentOrganism);
                    }
                } else {
                    foreach (['A', 'B', 'C'] as $type) {
                        if (count($cellsByType[$type]) == 3) {
                            $newGrid[$x][$y] = new Cell($x, $y, new Organism($type, $x, $y));
                            break;
                        }
                    }
                }
            }
        }

        $this->world->setCells($newGrid);
    }

    private function outputGrid(array $grid, int $dimension, SymfonyStyle $io): void
    {
        $tableRows = [];
        for ($x = 0; $x < $dimension; $x++) {
            $row = [];
            for ($y = 0; $y < $dimension; $y++) {
                $organism = $grid[$x][$y]->getOrganism();
                $row[] = $organism ? $organism->getType() : '.';
            }
            $tableRows[] = $row;
        }

        $io->section("Iteration completed");
        $io->table(
            array_fill(0, $dimension, ''), // Headers
            $tableRows  // Rows
        );
    }

    private function groupCellsByOrganismType(array $neighbors): array
    {
        $groupedCells = [
            'A' => [],
            'B' => [],
            'C' => []
        ];

        foreach ($neighbors as $neighbor) {
            $organism = $neighbor->getOrganism();
            if ($organism !== null) {
                $type = $organism->getType();
                if (isset($groupedCells[$type])) {
                    $groupedCells[$type][] = $neighbor;
                }
            }
        }

        return $groupedCells;
    }
}
