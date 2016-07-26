<?php

namespace AppBundle\Command;

use Exception;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportProductImagesCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('app:import_product_images')
                ->setDescription('Import product images')
                ->addArgument('file', InputArgument::REQUIRED, "Filename to import");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $service = $this->getContainer()->get('catalog_import_service');
        $output->write("Beginning product image import...\n");
        try {
            $service->importProductImages($input->getArgument('file'));
        } catch (Exception $e) {
            $output->writeln("There was an error importing product images: " . $e->getMessage());
        }
        $output->write("Finished!\n\n");
    }

}
