<?php
declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Controller\DataFetcherController;

class CountrySyncCommand extends Command
{

    private $controller;

    public function __construct(DataFetcherController $controller)
    {
        parent::__construct();
        $this->controller = $controller;
    }

    protected function configure(): void
    {
        $this->setName('countries:sync');
        $this->setDescription('Synchronize the countries');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->controller->setContainer($this->getApplication()->getKernel()->getContainer());
        $this->controller->fetchDataFromApi();

        $output->writeln('finished');

        return COMMAND::SUCCESS;
    }
}