<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * User
 *
 * @ORM\Table(name="fos_user")
 * @ORM\Entity
 */
class User extends BaseUser {
    
    const ROLE_CUSTOMER = 'ROLE_CUSTOMER';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     *
     * @var array
     * @ORM\Column(name="customer_numbers", type="array")
     */
    private $customerNumbers;

    public function __construct() {
        parent::__construct();
        $this->customerNumbers = array();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    public function getCustomerNumbers() {
        return $this->customerNumbers;
    }

    public function setCustomerNumbers($customerNumbers) {
        $this->customerNumbers = array();
        foreach ($customerNumbers as $customerNumber) {
            $this->addCustomerNumber($customerNumber);
        }
        if (sizeof($this->customerNumbers) == 0) {
            $this->removeRole(self::ROLE_CUSTOMER);
        }
        return $this;
    }

    public function addCustomerNumber($customerNumber) {
        if (!in_array($customerNumber, $this->customerNumbers, true)) {
            $this->customerNumbers[] = $customerNumber;
        }
        $this->addRole(self::ROLE_CUSTOMER);
    }

    public function removeCustomerNumber($customerNumber) {
        if (false !== $key = array_search($customerNumber, $this->customerNumbers, true)) {
            unset($this->customerNumbers[$key]);
            $this->customerNumbers = array_values($this->customerNumbers);
        }
        
        if (sizeof($this->customerNumbers) == 0) {
            $this->removeRole(self::ROLE_CUSTOMER);
        }

        return $this;
    }
    
    public function isAdmin() {
        return $this->hasRole(self::ROLE_ADMIN);
    }
    
    public function getAdmin() {
        return $this->hasRole(self::ROLE_ADMIN);
    }
    
    public function setAdmin($admin) {
        if ($admin) {
            $this->addRole(self::ROLE_ADMIN);
        } else {
            $this->removeRole(self::ROLE_ADMIN);
        }
        return $this;
    }

}
