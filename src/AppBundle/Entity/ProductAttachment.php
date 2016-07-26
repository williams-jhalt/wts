<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * ProductAttachment
 *
 * @ORM\Table(name="product_attachment")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductAttachmentRepository")
 */
class ProductAttachment {

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
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var bool
     *
     * @ORM\Column(name="explicit", type="boolean")
     */
    private $explicit;

    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="attachments")
     */
    private $product;

    /**
     *
     * @var UploadedFile
     */
    private $file;

    public function __construct() {
        $this->explicit = false;
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
     * Set url
     *
     * @param string $url
     *
     * @return ProductAttachment
     */
    public function setUrl($url) {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Set explicit
     *
     * @param boolean $explicit
     *
     * @return ProductAttachment
     */
    public function setExplicit($explicit) {
        $this->explicit = $explicit;

        return $this;
    }

    /**
     * Get explicit
     *
     * @return bool
     */
    public function getExplicit() {
        return $this->explicit;
    }

    public function getProduct() {
        return $this->product;
    }

    public function setProduct($product) {
        $this->product = $product;
        return $this;
    }

    public function getFile() {
        return $this->file;
    }

    public function setFile(UploadedFile $file) {
        $this->file = $file;
        return $this;
    }

}
