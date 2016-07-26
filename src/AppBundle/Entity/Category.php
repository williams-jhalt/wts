<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 */
class Category {

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
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Product", mappedBy="categories", fetch="EXTRA_LAZY")
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent", fetch="EXTRA_LAZY")
     * */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * */
    private $parent;

    public function __construct() {
        $this->products = new ArrayCollection();
        $this->children = new ArrayCollection();
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
     * Set code
     *
     * @param string $code
     *
     * @return Category
     */
    public function setCode($code) {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode() {
        return $this->code;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getProducts() {
        return $this->products;
    }

    public function getChildren() {
        return $this->children;
    }

    public function getParent() {
        return $this->parent;
    }

    public function setProducts($products) {
        $this->products = $products;
        return $this;
    }

    public function setChildren($children) {
        $this->children = $children;
        return $this;
    }

    public function setParent($parent) {
        $this->parent = $parent;
        return $this;
    }

}
