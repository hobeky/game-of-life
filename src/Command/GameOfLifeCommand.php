<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\GameService;
use App\Service\OrganismFactory;
use SimpleXMLElement;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'app:game-of-life',
    description: 'Add a short description for your command',
)]
class GameOfLifeCommand extends Command
{
    public function __construct(
        private readonly ParameterBagInterface $params,
        private readonly OrganismFactory $organismFactory,
        private readonly GameService $gameService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('print', null, InputOption::VALUE_NONE, 'If set, the task will print the XML file content')
            ->addArgument('file', InputArgument::OPTIONAL, 'The file to process', '/public/xml/config.xml');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $projectDir = $this->params->get('kernel.project_dir');
        $filename = $projectDir . $input->getArgument('file');

        if (! file_exists($filename)) {
            $io->error(sprintf('The file "%s" does not exist.', $filename));
            return Command::FAILURE;
        }

        $xmlContent = new SimpleXMLElement(file_get_contents($filename), 0, false);
        $squareLength = (int) $xmlContent->world->dimension;
        $iteration = (int) $xmlContent->world->iterationsCount;
        $organismsArray = [];
        foreach ($xmlContent->organisms->organism as $organism) {
            $organismsArray[] = [
                'x_pos' => (int) $organism->x_pos,
                'y_pos' => (int) $organism->y_pos,
                'species' => (string) $organism->species
            ];
        }

        $organisms = $this->organismFactory->createFromArray($organismsArray);

        $this->gameService->setWorldFromXmlFile(
            $iteration,
            $squareLength,
            $organisms
        );

        $this->gameService->createCellGrid($squareLength);
        $this->gameService->initialiseOrganisms($organisms);
        $this->gameService->runSimulation($input, $output);

        $io->success('Command executed successfully.');

        return Command::SUCCESS;
    }
}
