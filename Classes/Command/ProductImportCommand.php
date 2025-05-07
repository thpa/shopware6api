<?php
namespace ThomasPaul\Shopware6Api\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ThomasPaul\Shopware6Api\Service\ProductImportService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ProductImportCommand extends Command
{
    public function __construct(
        protected ProductImportService $importService
    ) {
        parent::__construct('shopware6api:import');
    }

    protected function configure(): void
    {
        $this->setDescription('Importiert Produkte aus der Shopware 6 API.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $output->writeln('Starte Import der Produkte...');
            
            $this->importService->import();
            
            $output->writeln('<info>Produkte wurden erfolgreich importiert.</info>');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>Fehler beim Import: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}