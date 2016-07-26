<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Shipment
 *
 * @ORM\Table(name="shipment")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ShipmentRepository")
 */
class Shipment {

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
     * @ORM\Column(name="manifest_id", type="string", length=255)
     */
    private $manifestId;

    /**
     * @ORM\OneToMany(targetEntity="ShipmentItem", mappedBy="shipment")
     */
    private $items;

    /**
     * @ORM\OneToMany(targetEntity="Package", mappedBy="shipment", fetch="EXTRA_LAZY")
     */
    private $packages;

    public function __construct() {
        $this->items = new ArrayCollection();
        $this->packages = new ArrayCollection();
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

    public function getManifestId() {
        return $this->manifestId;
    }

    public function getItems() {
        return $this->items;
    }

    public function getPackages() {
        return $this->packages;
    }

    public function setOrderNumber($orderNumber) {
        $this->orderNumber = $orderNumber;
        return $this;
    }

    public function setRecordSequence($recordSequence) {
        $this->recordSequence = $recordSequence;
        return $this;
    }

    public function setManifestId($manifestId) {
        $this->manifestId = $manifestId;
        return $this;
    }

    public function setItems($items) {
        $this->items = $items;
        return $this;
    }

    public function setPackages($packages) {
        $this->packages = $packages;
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
