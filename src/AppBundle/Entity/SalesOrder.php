<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * SalesOrder
 *
 * @ORM\Table(name="sales_order")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SalesOrderRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class SalesOrder {

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
     * @ORM\Column(name="ship_to_attention", type="string", length=255, nullable=true)
     */
    private $shipToAttention;

    /**
     * @var string
     *
     * @ORM\Column(name="ship_to_name", type="string", length=255, nullable=true)
     */
    private $shipToName;

    /**
     * @var string
     *
     * @ORM\Column(name="ship_to_address1", type="string", length=255, nullable=true)
     */
    private $shipToAddress1;

    /**
     * @var string
     *
     * @ORM\Column(name="ship_to_address2", type="string", length=255, nullable=true)
     */
    private $shipToAddress2;

    /**
     * @var string
     *
     * @ORM\Column(name="ship_to_address3", type="string", length=255, nullable=true)
     */
    private $shipToAddress3;

    /**
     * @var string
     *
     * @ORM\Column(name="ship_to_city", type="string", length=255, nullable=true)
     */
    private $shipToCity;

    /**
     * @var string
     *
     * @ORM\Column(name="ship_to_state", type="string", length=255, nullable=true)
     */
    private $shipToState;

    /**
     * @var string
     *
     * @ORM\Column(name="ship_to_postal_code", type="string", length=255, nullable=true)
     */
    private $shipToPostalCode;

    /**
     * @var string
     *
     * @ORM\Column(name="ship_to_country_code", type="string", length=255, nullable=true)
     */
    private $shipToCountryCode;

    /**
     * @var string
     *
     * @ORM\Column(name="ship_via_code", type="string", length=255, nullable=true)
     */
    private $shipViaCode;

    /**
     * @var string
     *
     * @ORM\Column(name="order_date", type="date")
     */
    private $orderDate;

    /**
     * @var string
     *
     * @ORM\Column(name="open", type="boolean")
     */
    private $open;

    /**
     * @var string
     *
     * @ORM\Column(name="order_gross_amount", type="decimal", scale=2)
     */
    private $orderGrossAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="customer_po", type="string", length=255, nullable=true)
     */
    private $customerPO;

    /**
     * @var string
     *
     * @ORM\Column(name="external_order_number", type="string", length=255, nullable=true)
     */
    private $externalOrderNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="keywords", type="text", nullable=true)
     */
    private $keywords;

    /**
     * @ORM\OneToMany(targetEntity="SalesOrderItem", mappedBy="salesOrder")
     * 
     * @var ArrayCollection
     */
    private $items;

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
     * @ORM\Column(name="credit", type="boolean")
     */
    private $credit;

    public function __construct() {
        $this->shipments = new ArrayCollection();
        $this->invoices = new ArrayCollection();
        $this->items = new ArrayCollection();
        $this->shipped = false;
        $this->invoiced = false;
        $this->credit = false;
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

    public function getCustomerNumber() {
        return $this->customerNumber;
    }

    public function getShipToAttention() {
        return $this->shipToAttention;
    }

    public function getShipToName() {
        return $this->shipToName;
    }

    public function getShipToAddress1() {
        return $this->shipToAddress1;
    }

    public function getShipToAddress2() {
        return $this->shipToAddress2;
    }

    public function getShipToAddress3() {
        return $this->shipToAddress3;
    }

    public function getShipToCity() {
        return $this->shipToCity;
    }

    public function getShipToState() {
        return $this->shipToState;
    }

    public function getShipToPostalCode() {
        return $this->shipToPostalCode;
    }

    public function getShipToCountryCode() {
        return $this->shipToCountryCode;
    }

    public function getShipViaCode() {
        return $this->shipViaCode;
    }

    public function getOrderDate() {
        return $this->orderDate;
    }

    public function getOpen() {
        return $this->open;
    }

    public function getOrderGrossAmount() {
        return $this->orderGrossAmount;
    }

    public function getCustomerPO() {
        return $this->customerPO;
    }

    public function getExternalOrderNumber() {
        return $this->externalOrderNumber;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getKeywords() {
        return $this->keywords;
    }

    public function getItems() {
        return $this->items;
    }

    public function getCreatedOn() {
        return $this->createdOn;
    }

    public function getUpdatedOn() {
        return $this->updatedOn;
    }

    public function getCredit() {
        return $this->credit;
    }

    public function setOrderNumber($orderNumber) {
        $this->orderNumber = $orderNumber;
        return $this;
    }

    public function setRecordSequence($recordSequence) {
        $this->recordSequence = $recordSequence;
        return $this;
    }

    public function setCustomerNumber($customerNumber) {
        $this->customerNumber = $customerNumber;
        return $this;
    }

    public function setShipToAttention($shipToAttention) {
        $this->shipToAttention = $shipToAttention;
        return $this;
    }

    public function setShipToName($shipToName) {
        $this->shipToName = $shipToName;
        return $this;
    }

    public function setShipToAddress1($shipToAddress1) {
        $this->shipToAddress1 = $shipToAddress1;
        return $this;
    }

    public function setShipToAddress2($shipToAddress2) {
        $this->shipToAddress2 = $shipToAddress2;
        return $this;
    }

    public function setShipToAddress3($shipToAddress3) {
        $this->shipToAddress3 = $shipToAddress3;
        return $this;
    }

    public function setShipToCity($shipToCity) {
        $this->shipToCity = $shipToCity;
        return $this;
    }

    public function setShipToState($shipToState) {
        $this->shipToState = $shipToState;
        return $this;
    }

    public function setShipToPostalCode($shipToPostalCode) {
        $this->shipToPostalCode = $shipToPostalCode;
        return $this;
    }

    public function setShipToCountryCode($shipToCountryCode) {
        $this->shipToCountryCode = $shipToCountryCode;
        return $this;
    }

    public function setShipViaCode($shipViaCode) {
        $this->shipViaCode = $shipViaCode;
        return $this;
    }

    public function setOrderDate($orderDate) {
        $this->orderDate = $orderDate;
        return $this;
    }

    public function setOpen($open) {
        $this->open = $open;
        return $this;
    }

    public function setOrderGrossAmount($orderGrossAmount) {
        $this->orderGrossAmount = $orderGrossAmount;
        return $this;
    }

    public function setCustomerPO($customerPO) {
        $this->customerPO = $customerPO;
        return $this;
    }

    public function setExternalOrderNumber($externalOrderNumber) {
        $this->externalOrderNumber = $externalOrderNumber;
        return $this;
    }

    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }

    public function setKeywords($keywords) {
        $this->keywords = $keywords;
        return $this;
    }

    public function setItems(ArrayCollection $items) {
        $this->items = $items;
        return $this;
    }

    public function setCreatedOn($createdOn) {
        $this->createdOn = $createdOn;
        return $this;
    }

    public function setUpdatedOn($updatedOn) {
        $this->updatedOn = $updatedOn;
        return $this;
    }

    public function setCredit($credit) {
        $this->credit = $credit;
        return $this;
    }

}
