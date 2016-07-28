<?php

namespace AppBundle\Command;

use Exception;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateOpenSalesOrdersCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('app:update_open_orders')
                ->setDescription('Update open orders');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $service = $this->getContainer()->get('erp_one_order_service');
        $output->write("Beginning erp order update...\n");
        try {
            $service->updateOpenSalesOrders();
            $service->updateOpenConsolidatedInvoices();
        } catch (Exception $e) {
            $output->writeln("There was an error updating open orders: " . $e->getMessage());
        }
        $output->write("Finished!\n\n");
    }

}
