<?php

namespace AppBundle\Command;

use Exception;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadNewOrdersCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('app:load_new_orders')
                ->setDescription('Load new orders');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $service = $this->getContainer()->get('erp_one_order_service');
        $output->write("Beginning erp order load...\n");
        try {
            $service->loadOrders();
            $service->loadConsolidatedInvoices();
        } catch (Exception $e) {
            $output->writeln("There was an error loading open orders: " . $e->getMessage());
        }
        $output->write("Finished!\n\n");
    }

}
