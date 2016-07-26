<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Invoice
 *
 * @ORM\Table(name="invoice")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InvoiceRepository")
 */
class Invoice {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="order_number", type="integer")
     */
    private $orderNumber;

    /**
     * @var int
     *
     * @ORM\Column(name="record_sequence", type="integer")
     */
    private $recordSequence;

    /**
     * @var string
     *
     * @ORM\Column(name="customer_number", type="string", length=255)
     */
    private $customerNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="gross_amount", type="decimal", scale=2, nullable=true)
     */
    private $grossAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="freight_charge", type="decimal", scale=2, nullable=true)
     */
    private $freightCharge;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_and_handling_charge", type="decimal", scale=2, nullable=true)
     */
    private $shippingAndHandlingCharge;

    /**
     * @var string
     *
     * @ORM\Column(name="net_amount", type="decimal", scale=2, nullable=true)
     */
    private $netAmount;

    /**
     * @ORM\OneToMany(targetEntity="InvoiceItem", mappedBy="invoice")
     */
    private $items;

    /**
     * @ORM\Column(name="open", type="boolean")
     */
    private $open;

    public function __construct() {
        $this->items = new ArrayCollection();
        $this->open = true;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    public function getOrderNumber() {
        return $this->orderNumber;
    }

    public function getRecordSequence() {
        return $this->recordSequence;
    }

    public function getGrossAmount() {
        return $this->grossAmount;
    }

    public function getFreightCharge() {
        return $this->freightCharge;
    }

    public function getShippingAndHandlingCharge() {
        return $this->shippingAndHandlingCharge;
    }

    public function getNetAmount() {
        return $this->netAmount;
    }

    public function getItems() {
        return $this->items;
    }

    public function getOpen() {
        return $this->open;
    }

    public function setOrderNumber($orderNumber) {
        $this->orderNumber = $orderNumber;
        return $this;
    }

    public function setRecordSequence($recordSequence) {
        $this->recordSequence = $recordSequence;
        return $this;
    }

    public function setGrossAmount($grossAmount) {
        $this->grossAmount = $grossAmount;
        return $this;
    }

    public function setFreightCharge($freightCharge) {
        $this->freightCharge = $freightCharge;
        return $this;
    }

    public function setShippingAndHandlingCharge($shippingAndHandlingCharge) {
        $this->shippingAndHandlingCharge = $shippingAndHandlingCharge;
        return $this;
    }

    public function setNetAmount($netAmount) {
        $this->netAmount = $netAmount;
        return $this;
    }

    public function setItems($items) {
        $this->items = $items;
        return $this;
    }

    public function setOpen($open) {
        $this->open = $open;
        return $this;
    }

    public function getCustomerNumber() {
        return $this->customerNumber;
    }

    public function setCustomerNumber($customerNumber) {
        $this->customerNumber = $customerNumber;
        return $this;
    }

}
