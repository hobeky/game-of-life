<?php

namespace App\Service;

use MongoDB\BSON\PackedArray;
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

    public function createCellGrid($dimension): void
    {
        $grid = array_fill(0, $dimension, array_fill(0, $dimension, null));
        for ($x = 0; $x < $dimension; $x++) {
            for ($y = 0; $y < $dimension; $y++) {
                $grid[$x][$y] = new Cell($x, $y);  // Assuming the Cell constructor can handle just coordinates
            }
        }
        $this->world->setCells($grid);
    }

    public function initialiseOrganisms($organisms)
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

    public function runSimulation(InputInterface $input, OutputInterface $output)
    {

        $io = new SymfonyStyle($input, $output);
        $cycles = $this->world->getMaxCycles();
        dump($this->world->getCells());
        for ($i = 0; $i < $cycles; $i++) {
            $this->simulateIteration();
            $grid = $this->world->getCells();
            $dimension = $this->world->getSquareLength();
//            dump($grid);
            $this->outputGrid($grid, $dimension, $io);
            $io->success($i);
            sleep(1);
        }
    }

//    tu sa zmenia cell na null
    public function simulateIteration()
    {
        $squareLength = $this->world->getSquareLength();
        $currentGrid = $this->world->getCells();
//        dump($currentGrid);
        $newGrid = array_fill(0, $squareLength, array_fill(0, $squareLength, null));

        foreach ($currentGrid as $rowKey => $row) {
            foreach ($row as $cellKey => $cell) {
                /** @var Cell $cell */
                if ($cell->getOrganism() instanceof Organism) {
                    $this->ifHasLessThanTwoNeighbors();
                    $this->ifHasTwoOrThreeNeighbors();
                    $this->ifHasMoreThanFourNeighbors();
                } else {
                    $newOrganism = $this->ifEmtpyCellHasThreeNeighbors($cell);
                }
                break(2);
// DO NOT FORGET CONFLICTS
            }
        }

        $this->world->setCells($newGrid);
    }

    private function ifEmtpyCellHasThreeNeighbors(Cell $cell): ?Organism
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

        if (!empty($groupOfCounterNeighbor)) {
            if(count($groupOfCounterNeighbor) <= 1) {
                return new Organism(
                    $groupOfCounterNeighbor[$key],
                    $cell->getPosX(),
                    $cell->getPosY()
                );
            } else {
                dd('handle more different neigbors');
            }

        }
        return null;
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
//        dump($grid);
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
