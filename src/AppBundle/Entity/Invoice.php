<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Invoice
 *
 * @ORM\Table(name="invoice")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InvoiceRepository")
 * @ORM\HasLifecycleCallbacks()
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

    /**
     * @ORM\Column(name="consolidated", type="boolean")
     */
    private $consolidated;

    /**
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @ORM\Column(name="updated_on", type="datetime", nullable=true)
     */
    private $updatedOn;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice_number", type="string", length=255, nullable=true)
     */
    private $invoiceNumber;

    /**
     * @ORM\Column(name="invoice_date", type="datetime", nullable=true)
     */
    private $invoiceDate;

    /**
     * @var string
     *
     * @ORM\Column(name="keywords", type="text", nullable=true)
     */
    private $keywords;

    public function __construct() {
        $this->items = new ArrayCollection();
        $this->open = true;
        $this->consolidated = false;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist() {
        $this->createdOn = new DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate() {
        $this->updatedOn = new DateTime();
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

    public function getConsolidated() {
        return $this->consolidated;
    }

    public function getInvoiceNumber() {
        return $this->invoiceNumber;
    }

    public function setConsolidated($consolidated) {
        $this->consolidated = $consolidated;
        return $this;
    }

    public function setInvoiceNumber($invoiceNumber) {
        $this->invoiceNumber = $invoiceNumber;
        return $this;
    }

    public function getCreatedOn() {
        return $this->createdOn;
    }

    public function getUpdatedOn() {
        return $this->updatedOn;
    }

    public function setCreatedOn($createdOn) {
        $this->createdOn = $createdOn;
        return $this;
    }

    public function setUpdatedOn($updatedOn) {
        $this->updatedOn = $updatedOn;
        return $this;
    }

    public function getInvoiceDate() {
        return $this->invoiceDate;
    }

    public function setInvoiceDate($invoiceDate) {
        $this->invoiceDate = $invoiceDate;
        return $this;
    }

    public function getKeywords() {
        return $this->keywords;
    }

    public function setKeywords($keywords) {
        $this->keywords = $keywords;
        return $this;
    }

}
