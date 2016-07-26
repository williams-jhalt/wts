<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductDetail
 *
 * @ORM\Table(name="product_detail")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductDetailRepository")
 */
class ProductDetail {

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
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(name="height", type="decimal", nullable=true, scale=3)
     */
    private $height;

    /**
     * @ORM\Column(name="length", type="decimal", nullable=true, scale=3)
     */
    private $length;

    /**
     * @ORM\Column(name="width", type="decimal", nullable=true, scale=3)
     */
    private $width;

    /**
     * @ORM\Column(name="weight", type="decimal", nullable=true, scale=3)
     */
    private $weight;

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return ProductDetail
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    public function getHeight() {
        return $this->height;
    }

    public function getLength() {
        return $this->length;
    }

    public function getWidth() {
        return $this->width;
    }

    public function getWeight() {
        return $this->weight;
    }

    public function setHeight($height) {
        $this->height = $height;
        return $this;
    }

    public function setLength($length) {
        $this->length = $length;
        return $this;
    }

    public function setWidth($width) {
        $this->width = $width;
        return $this;
    }

    public function setWeight($weight) {
        $this->weight = $weight;
        return $this;
    }

}
