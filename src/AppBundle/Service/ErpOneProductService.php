<?php

namespace AppBundle\Service;

use AppBundle\Entity\Product;
use AppBundle\Entity\Manufacturer;
use AppBundle\Entity\ProductType;
use DateTime;
use Doctrine\ORM\EntityManager;

class ErpOneProductService {

    /**
     * @var ErpOneConnectorService
     */
    private $_erp;

    /**
     * @var EntityManager
     */
    private $_em;

    public function __construct(ErpOneConnectorService $erp, EntityManager $em) {
        $this->_erp = $erp;
        $this->_em = $em;
    }

    public function updateProduct(Product $product) {

        $fields = "item.item,item.manufacturer,item.product_line,item.descr,item.date_added,wa_item.qty_oh,wa_item.list_price,item.upc1,item.sy_lookup";
        $query = "FOR EACH item NO-LOCK WHERE "
                . "item.company_it = '{$this->_erp->getCompany()}' AND item.web_item = yes AND item.item = '{$product->getItemNumber()}', "
                . "EACH wa_item NO-LOCK WHERE "
                . "wa_item.company_it = item.company_it AND wa_item.item = item.item";

        $response = $this->_erp->read($query, $fields, 0, 1);

        $x = $response[0];

        $product->setItemNumber($x->item_item);
        $product->setName(implode(" ", $x->item_descr));
        $product->setKeywords($x->item_sy_lookup);
        $product->setStockQuantity($x->wa_item_qty_oh);
        $product->setPrice($x->wa_item_list_price);
        if ($product->getReleaseDate() != new DateTime($x->item_date_added)) {
            $product->setReleaseDate(new DateTime($x->item_date_added));
        }

        $manufacturer = $this->_em->getRepository('AppBundle:Manufacturer')->findOneByCode($x->item_manufacturer);

        if ($manufacturer === null) {
            $manufacturer = new Manufacturer();
            $manufacturer->setCode($x->item_manufacturer);
            $manufacturer->setName($x->item_manufacturer);
            $this->_em->persist($manufacturer);
            $this->_em->flush($manufacturer);
        }

        $product->setManufacturer($manufacturer);

        $type = $this->_em->getRepository('AppBundle:ProductType')->findOneByCode($x->item_product_line);

        if ($type === null) {
            $type = new ProductType();
            $type->setCode($x->item_product_line);
            $type->setName($x->item_product_line);
            $this->_em->persist($type);
            $this->_em->flush($type);
        }

        $product->setType($type);
        $product->setActive(true);

        $this->_em->persist($product);
        $this->_em->flush();

        return $product;
    }

    public function loadProducts() {

        $fields = "item.item,item.manufacturer,item.product_line,item.descr,item.date_added,wa_item.qty_oh,wa_item.list_price,item.upc1,item.sy_lookup";
        $query = "FOR EACH item NO-LOCK WHERE "
                . "item.company_it = '{$this->_erp->getCompany()}' AND item.web_item = yes, "
                . "EACH wa_item NO-LOCK WHERE "
                . "wa_item.company_it = item.company_it AND wa_item.item = item.item";

        $offset = 0;
        $limit = 1000;

        do {

            $response = $this->_erp->read($query, $fields, $offset, $limit);

            foreach ($response as $erpObj) {

                $product = $this->_em->getRepository('AppBundle:Product')->findOneByItemNumber($erpObj->item_item);

                if ($product === null) {
                    $product = new Product();
                    $product->setItemNumber($erpObj->item_item);
                }

                $product->setStockQuantity($erpObj->wa_item_qty_oh);
                $product->setPrice($erpObj->wa_item_list_price);
                $product->setName(implode(" ", $erpObj->item_descr));
                $product->setKeywords($erpObj->item_sy_lookup);
                if ($product->getReleaseDate() != new DateTime($erpObj->item_date_added)) {
                    $product->setReleaseDate(new DateTime($erpObj->item_date_added));
                }

                $manufacturer = $this->_em->getRepository('AppBundle:Manufacturer')->findOneByCode($erpObj->item_manufacturer);

                if ($manufacturer === null) {
                    $manufacturer = new Manufacturer();
                    $manufacturer->setCode($erpObj->item_manufacturer);
                    $manufacturer->setName($erpObj->item_manufacturer);
                    $this->_em->persist($manufacturer);
                    $this->_em->flush($manufacturer);
                }

                $product->setManufacturer($manufacturer);

                $type = $this->_em->getRepository('AppBundle:ProductType')->findOneByCode($erpObj->item_product_line);

                if ($type === null) {
                    $type = new ProductType();
                    $type->setCode($erpObj->item_product_line);
                    $type->setName($erpObj->item_product_line);
                    $this->_em->persist($type);
                    $this->_em->flush($type);
                }

                $product->setType($type);
                $product->setActive(true);

                $this->_em->persist($product);
            }

            $this->_em->flush();
            $this->_em->clear();

            $offset = $offset + $limit;
        } while (!empty($response));
    }

}
