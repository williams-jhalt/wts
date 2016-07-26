<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Product {

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
     * @ORM\Column(name="item_number", type="string", length=255, unique=true)
     */
    private $itemNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="release_date", type="date", nullable=true)
     */
    private $releaseDate;

    /**
     * @var string
     *
     * @ORM\Column(name="keywords", type="text", nullable=true)
     */
    private $keywords;

    /**
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @ORM\Column(name="updated_on", type="datetime", nullable=true)
     */
    private $updatedOn;

    /**
     * @ORM\ManyToOne(targetEntity="ProductType", inversedBy="products")
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="Manufacturer", inversedBy="products")
     */
    private $manufacturer;

    /**
     * @ORM\OneToMany(targetEntity="ProductAttachment", mappedBy="product", fetch="EXTRA_LAZY")
     * 
     * @var ArrayCollection
     */
    private $attachments;

    /**
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="products")
     * */
    private $categories;

    /**
     * @ORM\Column(name="price", type="decimal", scale=2)
     */
    private $price;

    /**
     * @ORM\Column(name="stock_quantity", type="integer")
     */
    private $stockQuantity;

    /**
     * @ORM\OneToOne(targetEntity="ProductDetail", cascade={"persist","remove"})
     */
    private $detail;

    /**
     * @ORM\Column(name="manufacturer_item_number", type="string", length=255, nullable=true)
     */
    private $manufacturerItemNumber;

    /**
     * @ORM\Column(name="barcode", type="string", length=255, nullable=true)
     */
    private $barcode;

    /**
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    public function __construct() {
        $this->attachments = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->detail = new ProductDetail();
        $this->price = 0.0;
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

    /**
     * Set itemNumber
     *
     * @param string $itemNumber
     *
     * @return Product
     */
    public function setItemNumber($itemNumber) {
        $this->itemNumber = $itemNumber;

        return $this;
    }

    /**
     * Get itemNumber
     *
     * @return string
     */
    public function getItemNumber() {
        return $this->itemNumber;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Product
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set releaseDate
     *
     * @param DateTime $releaseDate
     *
     * @return Product
     */
    public function setReleaseDate($releaseDate) {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    /**
     * Get releaseDate
     *
     * @return DateTime
     */
    public function getReleaseDate() {
        return $this->releaseDate;
    }

    public function getKeywords() {
        return $this->keywords;
    }

    public function setKeywords($keywords) {
        $this->keywords = $keywords;
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

    public function getType() {
        return $this->type;
    }

    public function getManufacturer() {
        return $this->manufacturer;
    }

    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    public function setManufacturer($manufacturer) {
        $this->manufacturer = $manufacturer;
        return $this;
    }

    public function getAttachments() {
        return $this->attachments;
    }

    public function setAttachments(ArrayCollection $attachments) {
        $this->attachments = $attachments;
        return $this;
    }

    public function getCategories() {
        return $this->categories;
    }

    public function setCategories($categories) {
        $this->categories = $categories;
        return $this;
    }

    public function getPrice() {
        return $this->price;
    }

    public function getStockQuantity() {
        return $this->stockQuantity;
    }

    public function setPrice($price) {
        $this->price = $price;
        return $this;
    }

    public function setStockQuantity($stockQuantity) {
        $this->stockQuantity = $stockQuantity;
        return $this;
    }

    public function getDetail() {
        return $this->detail;
    }

    public function setDetail($detail) {
        $this->detail = $detail;
        return $this;
    }

    public function getManufacturerItemNumber() {
        return $this->manufacturerItemNumber;
    }

    public function getBarcode() {
        return $this->barcode;
    }

    public function getActive() {
        return $this->active;
    }

    public function setManufacturerItemNumber($manufacturerItemNumber) {
        $this->manufacturerItemNumber = $manufacturerItemNumber;
        return $this;
    }

    public function setBarcode($barcode) {
        $this->barcode = $barcode;
        return $this;
    }

    public function setActive($active) {
        $this->active = $active;
        return $this;
    }

}
