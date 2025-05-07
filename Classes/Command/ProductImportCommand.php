<?php
namespace ThomasPaul\Shopware6Api\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ThomasPaul\Shopware6Api\Service\ProductImportService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ProductImportCommand extends Command
{
    protected static $defaultName = 'shopware6api:import';

    public function __construct(
        protected ProductImportService $importService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Import products from Shopware 6');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $output->writeln('Starting product import...');
            
            $this->importService->import();
            
            $output->writeln('<info>Import completed successfully!</info>');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>Import failed: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}