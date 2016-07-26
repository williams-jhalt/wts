<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Package
 *
 * @ORM\Table(name="package")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PackageRepository")
 */
class Package {

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
     * @ORM\Column(name="tracking_number", type="string", length=255)
     */
    private $trackingNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_cost", type="decimal", precision=10, scale=2)
     */
    private $shippingCost;

    /**
     * @ORM\ManyToOne(targetEntity="Shipment", inversedBy="packages")
     */
    private $shipment;

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    public function getTrackingNumber() {
        return $this->trackingNumber;
    }

    public function getShippingCost() {
        return $this->shippingCost;
    }

    public function getShipment() {
        return $this->shipment;
    }

    public function setTrackingNumber($trackingNumber) {
        $this->trackingNumber = $trackingNumber;
        return $this;
    }

    public function setShippingCost($shippingCost) {
        $this->shippingCost = $shippingCost;
        return $this;
    }

    public function setShipment($shipment) {
        $this->shipment = $shipment;
        return $this;
    }

}
