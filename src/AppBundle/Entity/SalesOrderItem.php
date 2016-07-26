<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SalesOrderItem
 *
 * @ORM\Table(name="sales_order_item")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SalesOrderItemRepository")
 */
class SalesOrderItem {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="item_number", type="string", length=255)
     */
    private $itemNumber;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity_ordered", type="integer")
     */
    private $quantityOrdered;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity_shipped", type="integer")
     */
    private $quantityShipped;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity_billed", type="integer")
     */
    private $quantityBilled;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=2)
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity="SalesOrder", inversedBy="items")
     */
    private $salesOrder;

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    public function getItemNumber() {
        return $this->itemNumber;
    }

    public function getQuantityOrdered() {
        return $this->quantityOrdered;
    }

    public function getQuantityShipped() {
        return $this->quantityShipped;
    }

    public function getQuantityBilled() {
        return $this->quantityBilled;
    }

    public function getName() {
        return $this->name;
    }

    public function getPrice() {
        return $this->price;
    }

    public function getSalesOrder() {
        return $this->salesOrder;
    }

    public function setItemNumber($itemNumber) {
        $this->itemNumber = $itemNumber;
        return $this;
    }

    public function setQuantityOrdered($quantityOrdered) {
        $this->quantityOrdered = $quantityOrdered;
        return $this;
    }

    public function setQuantityShipped($quantityShipped) {
        $this->quantityShipped = $quantityShipped;
        return $this;
    }

    public function setQuantityBilled($quantityBilled) {
        $this->quantityBilled = $quantityBilled;
        return $this;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function setPrice($price) {
        $this->price = $price;
        return $this;
    }

    public function setSalesOrder($salesOrder) {
        $this->salesOrder = $salesOrder;
        return $this;
    }

}
