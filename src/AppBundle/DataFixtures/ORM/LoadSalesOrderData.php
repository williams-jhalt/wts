<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\SalesOrder;
use DateTime;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadSalesOrderData implements FixtureInterface {

    public function load(ObjectManager $manager) {

        $salesOrder = new SalesOrder();
        $salesOrder->setOrderNumber(3338605);
        $salesOrder->setRecordSequence(0);
        $salesOrder->setCustomerNumber('INIT');
        $salesOrder->setOpen(false);
        $salesOrder->setOrderDate(new DateTime());
        $salesOrder->setOrderGrossAmount(0);

        $manager->persist($salesOrder);

        $manager->flush();
    }

}
