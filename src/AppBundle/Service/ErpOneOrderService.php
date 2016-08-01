<?php

namespace AppBundle\Service;

use AppBundle\Entity\Invoice;
use AppBundle\Entity\InvoiceItem;
use AppBundle\Entity\Package;
use AppBundle\Entity\SalesOrder;
use AppBundle\Entity\SalesOrderItem;
use AppBundle\Entity\Shipment;
use AppBundle\Entity\ShipmentItem;
use DateTime;
use Doctrine\ORM\EntityManager;

class ErpOneOrderService {

    /**
     * @var ErpOneConnectorService
     */
    private $_erp;

    /**
     * @var EntityManager
     */
    private $_em;
    private $headerFields = "oe_head.customer,oe_head.cu_po,oe_head.ord_ext,oe_head.sy_lookup,oe_head.opn,oe_head.ord_date,oe_head.o_tot_gross,oe_head.order,oe_head.rec_seq,oe_head.ship_atn,oe_head.name,oe_head.adr,oe_head.state,oe_head.country_code,oe_head.postal_code,oe_head.ship_via_code,oe_head.stat,oe_head.Manifest_id,oe_head.c_tot_gross,oe_head.c_tot_net_ar,oe_head.c_tot_code_amt,oe_head.rec_type,oe_head.ar_type,oe_head.consolidated_order,oe_head.invoice";
    private $itemFields = "oe_line.item,oe_line.descr,oe_line.price,oe_line.q_ord,oe_line.q_comm,oe_line.q_itd,oe_line.rec_type";

    public function __construct(ErpOneConnectorService $erp, EntityManager $em) {
        $this->_erp = $erp;
        $this->_em = $em;
    }

    private function _loadFromErp($oeHeadResponse, $oeLineResponse = null, $oeShipPackResponse = null) {

        if ($oeLineResponse === null) {
            $oeLineResponse = array();
        }

        if ($oeShipPackResponse === null) {
            $oeShipPackResponse = array();
        }

        foreach ($oeHeadResponse as $erpObj) {

            if ($erpObj->oe_head_rec_type == "O") {

                $salesOrder = $this->_em->getRepository('AppBundle:SalesOrder')->findOneBy(array('orderNumber' => $erpObj->oe_head_order, 'recordSequence' => $erpObj->oe_head_rec_seq));

                if ($salesOrder === null) {
                    $salesOrder = new SalesOrder();
                }

                $salesOrder->setCustomerNumber($erpObj->oe_head_customer);
                $salesOrder->setCustomerPO($erpObj->oe_head_cu_po);
                $salesOrder->setExternalOrderNumber($erpObj->oe_head_ord_ext);
                $salesOrder->setKeywords($erpObj->oe_head_sy_lookup);
                $salesOrder->setOpen($erpObj->oe_head_opn);
                $salesOrder->setOrderDate(new DateTime($erpObj->oe_head_ord_date));
                $salesOrder->setOrderGrossAmount($erpObj->oe_head_o_tot_gross);
                $salesOrder->setOrderNumber($erpObj->oe_head_order);
                $salesOrder->setRecordSequence($erpObj->oe_head_rec_seq);
                $salesOrder->setShipToAttention($erpObj->oe_head_ship_atn);
                $salesOrder->setShipToName($erpObj->oe_head_name);
                $salesOrder->setShipToAddress1($erpObj->oe_head_adr[0]);
                $salesOrder->setShipToAddress2($erpObj->oe_head_adr[1]);
                $salesOrder->setShipToAddress3($erpObj->oe_head_adr[2]);
                $salesOrder->setShipToCity($erpObj->oe_head_adr[3]);
                $salesOrder->setShipToState($erpObj->oe_head_state);
                $salesOrder->setShipToCountryCode($erpObj->oe_head_country_code);
                $salesOrder->setShipToPostalCode($erpObj->oe_head_postal_code);
                $salesOrder->setShipViaCode($erpObj->oe_head_ship_via_code);
                $salesOrder->setStatus($erpObj->oe_head_stat);
                $salesOrder->setCredit(($erpObj->oe_head_ar_type == 'CI') ? true : false);

                foreach ($oeLineResponse as $erpItem) {
                    if ($erpItem->oe_line_rec_type == "O") {
                        $item = $this->_em->getRepository('AppBundle:SalesOrderItem')->findOneBy(array('salesOrder' => $salesOrder, 'itemNumber' => $erpItem->oe_line_item));
                        if ($item === null) {
                            $item = new SalesOrderItem();
                        }
                        $item->setItemNumber($erpItem->oe_line_item);
                        $item->setName(implode(" ", $erpItem->oe_line_descr));
                        $item->setPrice($erpItem->oe_line_price);
                        $item->setQuantityOrdered($erpItem->oe_line_q_ord);
                        $item->setQuantityShipped($erpItem->oe_line_q_comm);
                        $item->setQuantityBilled($erpItem->oe_line_q_itd);
                        $item->setSalesOrder($salesOrder);
                        $this->_em->persist($item);
                    }
                }

                $this->_em->persist($salesOrder);
            }
        }

        $this->_em->flush();
        $this->_em->clear();

        foreach ($oeHeadResponse as $erpObj) {

            // load shipments
            if ($erpObj->oe_head_rec_type == "S") {

                $shipment = $this->_em->getRepository('AppBundle:Shipment')->findOneBy(array('orderNumber' => $erpObj->oe_head_order, 'recordSequence' => $erpObj->oe_head_rec_seq));

                if ($shipment === null) {
                    $shipment = new Shipment();
                    $shipment->setCustomerNumber($erpObj->oe_head_customer);
                    $shipment->setOrderNumber($erpObj->oe_head_order);
                    $shipment->setRecordSequence($erpObj->oe_head_rec_seq);
                    $shipment->setManifestId($erpObj->oe_head_Manifest_id);
                }

                foreach ($oeLineResponse as $erpItem) {
                    if ($erpItem->oe_line_rec_type == "S") {
                        $item = $this->_em->getRepository('AppBundle:ShipmentItem')->findOneBy(array('shipment' => $shipment, 'itemNumber' => $erpItem->oe_line_item));
                        if ($item === null) {
                            $item = new ShipmentItem();
                        }
                        $item->setItemNumber($erpItem->oe_line_item);
                        $item->setName(implode(" ", $erpItem->oe_line_descr));
                        $item->setPrice($erpItem->oe_line_price);
                        $item->setQuantityOrdered($erpItem->oe_line_q_ord);
                        $item->setQuantityShipped($erpItem->oe_line_q_comm);
                        $item->setShipment($shipment);
                        $this->_em->persist($item);
                    }
                }

                foreach ($oeShipPackResponse as $erpPkg) {
                    if (preg_match('/Verify.*/', $erpPkg->tracking_no)) {
                        continue;
                    }
                    $package = $this->_em->getRepository('AppBundle:Package')->findOneBy(array('shipment' => $shipment, 'trackingNumber' => $erpPkg->tracking_no));
                    if ($package === null) {
                        $package = new Package();
                    }
                    $package->setTrackingNumber($erpPkg->tracking_no);
                    $package->setShippingCost($erpPkg->pkg_chg);
                    $package->setShipment($shipment);
                    $this->_em->persist($package);
                }

                $this->_em->persist($shipment);
            }

            // load invoices
            if ($erpObj->oe_head_rec_type == "I") {

                $invoice = $this->_em->getRepository('AppBundle:Invoice')->findOneBy(array('orderNumber' => $erpObj->oe_head_order, 'recordSequence' => $erpObj->oe_head_rec_seq));

                if ($invoice === null) {
                    $invoice = new Invoice();
                    $invoice->setCustomerNumber($erpObj->oe_head_customer);
                    $invoice->setOrderNumber($erpObj->oe_head_order);
                    $invoice->setRecordSequence($erpObj->oe_head_rec_seq);
                    $invoice->setGrossAmount($erpObj->oe_head_c_tot_gross);
                    $invoice->setNetAmount($erpObj->oe_head_c_tot_net_ar);
                    $invoice->setFreightCharge($erpObj->oe_head_c_tot_code_amt[0]);
                    $invoice->setShippingAndHandlingCharge($erpObj->oe_head_c_tot_code_amt[1]);
                    $invoice->setOpen($erpObj->oe_head_opn);
                    $invoice->setInvoiceNumber($erpObj->oe_head_invoice);
                    $invoice->setConsolidated($erpObj->oe_head_consolidated_order);
                }

                foreach ($oeLineResponse as $erpItem) {
                    if ($erpItem->oe_line_rec_type == "I") {
                        $item = $this->_em->getRepository('AppBundle:InvoiceItem')->findOneBy(array('invoice' => $invoice, 'itemNumber' => $erpItem->oe_line_item));
                        if ($item === null) {
                            $item = new InvoiceItem();
                        }
                        $item->setItemNumber($erpItem->oe_line_item);
                        $item->setName(implode(" ", $erpItem->oe_line_descr));
                        $item->setPrice($erpItem->oe_line_price);
                        $item->setQuantityOrdered($erpItem->oe_line_q_ord);
                        $item->setQuantityShipped($erpItem->oe_line_q_comm);
                        $item->setQuantityBilled($erpItem->oe_line_q_itd);
                        $item->setInvoice($invoice);
                        $this->_em->persist($item);
                    }
                }

                $this->_em->persist($invoice);
            }
        }

        $this->_em->flush();
        $this->_em->clear();
    }

    public function updateSalesOrder(SalesOrder $salesOrder) {

        $response = $this->_erp->read("FOR EACH oe_head NO-LOCK WHERE company_oe = '{$this->_erp->getCompany()}' AND order = '{$salesOrder->getOrderNumber()}'", $this->headerFields);
        $itemResponse = $this->_erp->read("FOR EACH oe_line NO-LOCK WHERE company_oe = '{$this->_erp->getCompany()}' AND order = '{$salesOrder->getOrderNumber()}'", $this->itemFields);
        $packageResponse = $this->_erp->read("FOR EACH oe_ship_pack NO-LOCK WHERE company_oe = '{$this->_erp->getCompany()}' AND order = '{$salesOrder->getOrderNumber()}'", "*");

        $this->_loadFromErp($response, $itemResponse, $packageResponse);

        return $salesOrder;
    }

    public function loadOpenSalesOrders() {

        $offset = 0;
        $limit = 1000;

        do {

            $response = $this->_erp->read("FOR EACH oe_head NO-LOCK WHERE company_oe = '{$this->_erp->getCompany()}' AND opn = yes AND rec_type = 'O' AND order > 0", $this->headerFields, $offset, $limit);

            foreach ($response as $erpObj) {

                $salesOrder = $this->_em->getRepository('AppBundle:SalesOrder')->findOneBy(array('orderNumber' => $erpObj->oe_head_order, 'recordSequence' => $erpObj->oe_head_rec_seq));

                if ($salesOrder === null) {
                    $salesOrder = new SalesOrder();
                    $salesOrder->setOrderNumber($erpObj->oe_head_order);
                    $salesOrder->setRecordSequence($erpObj->oe_head_rec_seq);
                }

                $salesOrder->setCustomerNumber($erpObj->oe_head_customer);
                $salesOrder->setCustomerPO($erpObj->oe_head_cu_po);
                $salesOrder->setExternalOrderNumber($erpObj->oe_head_ord_ext);
                $salesOrder->setKeywords($erpObj->oe_head_sy_lookup);
                $salesOrder->setOpen($erpObj->oe_head_opn);
                $salesOrder->setOrderDate(new DateTime($erpObj->oe_head_ord_date));
                $salesOrder->setOrderGrossAmount($erpObj->oe_head_o_tot_gross);
                $salesOrder->setShipToAddress1($erpObj->oe_head_adr[0]);
                $salesOrder->setShipToAddress2($erpObj->oe_head_adr[1]);
                $salesOrder->setShipToAddress3($erpObj->oe_head_adr[2]);
                $salesOrder->setShipToCity($erpObj->oe_head_adr[3]);
                $salesOrder->setShipToState($erpObj->oe_head_state);
                $salesOrder->setShipToCountryCode($erpObj->oe_head_country_code);
                $salesOrder->setShipToPostalCode($erpObj->oe_head_postal_code);
                $salesOrder->setShipViaCode($erpObj->oe_head_ship_via_code);
                $salesOrder->setStatus($erpObj->oe_head_stat);
                $salesOrder->setCredit(($erpObj->oe_head_ar_type == 'CI') ? true : false);

                $this->_em->persist($salesOrder);
            }

            $this->_em->flush();
            $this->_em->clear();

            $offset = $offset + $limit;
        } while (!empty($response));
    }

    public function updateOpenSalesOrders() {

        $openSalesOrders = $this->_em->getRepository('AppBundle:SalesOrder')
                ->createQueryBuilder('o')
                ->where('o.open = true')
                ->andWhere('o.updatedOn < :timeAgo OR o.updatedOn is null')
                ->setParameter('timeAgo', new DateTime('now -15 minutes'))
                ->getQuery()
                ->getResult();

        $ch = curl_init();

        foreach ($openSalesOrders as $t) {

            $oeHeadResponse = $this->_erp->read("FOR EACH oe_head NO-LOCK WHERE company_oe = '{$this->_erp->getCompany()}' AND order = '{$t->getOrderNumber()}'", $this->headerFields, 0, 5000, $ch);
            $oeLineResponse = $this->_erp->read("FOR EACH oe_line NO-LOCK WHERE company_oe = '{$this->_erp->getCompany()}' AND order = '{$t->getOrderNumber()}'", $this->itemFields, 0, 5000, $ch);
            $oeShipPackResponse = $this->_erp->read("FOR EACH oe_ship_pack NO-LOCK WHERE company_oe = '{$this->_erp->getCompany()}' AND order = '{$t->getOrderNumber()}'", "*", 0, 5000, $ch);

            $this->_loadFromErp($oeHeadResponse, $oeLineResponse, $oeShipPackResponse);
        }

        curl_close($ch);
    }

    public function loadOrders() {

        $lastOrder = $this->_em->createQuery("SELECT o.orderNumber FROM AppBundle:SalesOrder o ORDER BY o.orderNumber DESC")->setMaxResults(1)->getSingleScalarResult();

        $ch = curl_init();

        $start = 0;
        $count = 1000;

        do {

            $oeHeadResponse = $this->_erp->read("FOR EACH oe_head NO-LOCK WHERE company_oe = '{$this->_erp->getCompany()}' AND order > '{$lastOrder}'", $this->headerFields, $start, $count, $ch);

            if (!empty($oeHeadResponse)) {
                $this->_loadFromErp($oeHeadResponse);
            }

            $start = $start + $count;
        } while (!empty($oeHeadResponse));

        curl_close($ch);
    }

    public function loadConsolidatedInvoices() {

        $ch = curl_init();

        $oeHeadResponse = $this->_erp->read("FOR EACH oe_head NO-LOCK WHERE company_oe = '{$this->_erp->getCompany()}' AND invc_date > today - 7 AND consolidated_order = yes", $this->headerFields);

        $this->_loadFromErp($oeHeadResponse);

        curl_close($ch);
        
    }
    
    public function updateOpenConsolidatedInvoices() {

        $openConsInv = $this->_em->getRepository('AppBundle:Invoice')
                ->createQueryBuilder('o')
                ->where('SIZE(o.items) = 0')
                ->andWhere('o.consolidated = true')
                ->andWhere('o.updatedOn < :timeAgo OR o.updatedOn is null')
                ->setParameter('timeAgo', new DateTime('now -15 minutes'))
                ->getQuery()
                ->getResult();

        $ch = curl_init();

        foreach ($openConsInv as $t) {

            $oeHeadResponse = $this->_erp->read("FOR EACH oe_head NO-LOCK WHERE company_oe = '{$this->_erp->getCompany()}' AND rec_type = 'I' AND order = '{$t->getOrderNumber()}'", $this->headerFields, 0, 5000, $ch);
            $oeLineResponse = $this->_erp->read("FOR EACH oe_line NO-LOCK WHERE company_oe = '{$this->_erp->getCompany()}' AND rec_type = 'I' AND order = '{$t->getOrderNumber()}'", $this->itemFields, 0, 5000, $ch);            

            $this->_loadFromErp($oeHeadResponse, $oeLineResponse);
        }

        curl_close($ch);
        
    }

}
