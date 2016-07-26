<?php

namespace AppBundle\Command;

use Exception;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadProductsCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('app:load_products')
                ->setDescription('Load products');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $service = $this->getContainer()->get('erp_one_product_service');
        $output->write("Beginning erp product load...\n");
        try {
            $service->loadProducts();
        } catch (Exception $e) {
            $output->writeln("There was an error loading products: " . $e->getMessage());
        }
        $output->write("Finished!\n\n");
    }

}
