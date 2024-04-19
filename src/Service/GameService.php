<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GameService
{
    /**
     * @var World $world
     */
    private readonly World $world;

    private readonly SymfonyStyle $io;

    public function __construct()
    {
        $this->world = (new WorldFactory())->createWorld();
    }

    public function setWorldFromXmlFile(int $maxCycles, int $squareLength, array $organism): void
    {
        $this->world->setMaxCycles($maxCycles);
        $this->world->setSquareLength($squareLength);
        $this->world->setOrganisms($organism);
    }

    public function createCellGrid(int $dimension): void
    {
        $grid = array_fill(0, $dimension, array_fill(0, $dimension, null));
        for ($x = 0; $x < $dimension; $x++) {
            for ($y = 0; $y < $dimension; $y++) {
                $grid[$x][$y] = new Cell($x, $y);
            }
        }
        $this->world->setCells($grid);
    }

    /**
     * @param Organism[] $organisms
     * @return void
     */
    public function initialiseOrganisms(array $organisms)
    {
        $cells = $this->world->getCells();
        $organisms = $this->world->getOrganisms();

        foreach ($organisms as $oneOganism) {
            $x = $oneOganism->getPosX();
            $y = $oneOganism->getPosY();
            $name = $oneOganism->getName();

            $objectOrganism = new Organism($name, $x, $y);

            if (isset($cells[$x][$y])) {
                $cells[$x][$y]->setOrganism($objectOrganism);
            }
        }
    }

    public function runSimulation(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $cycles = $this->world->getMaxCycles();
        for ($i = 0; $i < $cycles; $i++) {
            $this->simulateIteration();
            $grid = $this->world->getCells();
            $dimension = $this->world->getSquareLength();
            $this->outputGrid($grid, $dimension, $io);
            $io->success($i);
            sleep(1);
        }
    }

    private function simulateIteration(): void
    {
        $squareLength = $this->world->getSquareLength();
        $currentGrid = $this->world->getCells();
        $newGrid = array_fill(0, $squareLength, array_fill(0, $squareLength, null));

        foreach ($currentGrid as $rowKey => $row) {
            foreach ($row as $cellKey => $cell) {
                /** @var Cell $cell */
                if ($cell->getOrganism() instanceof Organism) {
                    $newGrid[$rowKey][$cellKey] = $this->ifLessThanTwoOrMoreThanFour($cell);
                } else {
                    $newGrid[$rowKey][$cellKey] = $this->ifEmtpyCellHasThreeNeighbors($cell);
                }
            }
        }

        $this->world->setCells($newGrid);
    }

    private function ifLessThanTwoOrMoreThanFour(Cell $cell): Cell
    {
        $neighbors = $this->getNeighbors($cell);
        $groupedNeighbors = $this->groupCellsByOrganismType($neighbors);

        if (
            count($groupedNeighbors[$cell->getOrganism()->getName()]) > 3 ||
            count($groupedNeighbors[$cell->getOrganism()->getName()]) < 2
        ) {
            $cell->setOrganism(null);
        }

        return $cell;
    }

    private function ifEmtpyCellHasThreeNeighbors(Cell $cell): Cell
    {
        $neighbors = $this->getNeighbors($cell);
        $groupedNeighbors = $this->groupCellsByOrganismType($neighbors);
        $groupOfCounterNeighbor = [
            'A' => sizeof($groupedNeighbors['A']),
            'B' => sizeof($groupedNeighbors['B']),
            'C' => sizeof($groupedNeighbors['C'])
        ];

        foreach ($groupOfCounterNeighbor as $key => $typeOrganismGroup) {
            if ($typeOrganismGroup !== 3) {
                unset($groupOfCounterNeighbor[$key]);
            }
        }

        if (! empty($groupOfCounterNeighbor)) {
            $cell->setOrganism(new Organism(
                array_rand($groupOfCounterNeighbor),
                $cell->getPosX(),
                $cell->getPosY()
            ));
        }

        return $cell;
    }

    private function getNeighbors(Cell $cell): array
    {
        $neighbors = [];
        $neighborOffset = [
            ['x' => +1, 'y' => +1],
            ['x' => +1, 'y' => 0],
            ['x' => +1, 'y' => -1],
            ['x' => -1, 'y' => +1],
            ['x' => -1, 'y' => 0],
            ['x' => -1, 'y' => -1],
            ['x' => 0, 'y' => +1],
            ['x' => 0, 'y' => -1]
        ];
        $mapGrid = $this->world->getCells();
        $posX = $cell->getPosX();
        $posY = $cell->getPosY();

        foreach ($neighborOffset as $offset) {
            if (isset($mapGrid[$posX + $offset['x']][$posY + $offset['y']])) {
                $neighbors[] = $mapGrid[$posX + $offset['x']][$posY + $offset['y']];
            }
        }

        return $neighbors;
    }

    private function outputGrid(array $grid, int $dimension, SymfonyStyle $io): void
    {
        $tableRows = [];
        for ($x = 0; $x < $dimension; $x++) {
            $row = [];
            for ($y = 0; $y < $dimension; $y++) {
                $cell = $grid[$x][$y];
                $organism = null;
                if ($cell !== null) {
                    $organism = $grid[$x][$y]->getOrganism();
                }

                $row[] = $organism ? $organism->getName() : '.';
            }
            $tableRows[] = $row;
        }

        $io->section("Iteration completed");
        $io->table(
            array_fill(0, $dimension, ''), // Headers
            $tableRows  // Rows
        );
    }

    /**
     * @param Cell[] $neighbors
     * @return array[]
     */
    private function groupCellsByOrganismType(array $neighbors): array
    {
        $groupedCells = [
            'A' => [],
            'B' => [],
            'C' => []
        ];

        foreach ($neighbors as $neighbor) {
            $organism = $neighbor->getOrganism();
            if ($organism instanceof Organism) {
                $type = $organism->getName();
                if (isset($groupedCells[$type])) {
                    $groupedCells[$type][] = $neighbor;
                }
            }
        }

        return $groupedCells;
    }
}
