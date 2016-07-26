<?php

namespace AppBundle\Command;

use Exception;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCategoriesCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('app:import_categories')
                ->setDescription('Import categories')
                ->addArgument('file', InputArgument::REQUIRED, "Filename to import");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $service = $this->getContainer()->get('catalog_import_service');
        $output->write("Beginning category import...\n");
        try {
            $service->importCategories($input->getArgument('file'));
        } catch (Exception $e) {
            $output->writeln("There was an error importing categories: " . $e->getMessage());
        }
        $output->write("Finished!\n\n");
    }

}
