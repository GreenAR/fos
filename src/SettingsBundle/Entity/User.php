<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 7/4/17
 * Time: 11:23 AM
 */

namespace SettingsBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends BaseUser

{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(type="string",name="firstname",nullable=true)
     */
    protected $firstname;
    /**
     * @ORM\Column(type="string",name="lastname",nullable=true)
     */
    protected $lastname;
    /**
     * @ORM\Column(type="datetime",name="creationDate",nullable=true)
     */
    protected $creationDate;

    /**
     * @return mixed
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * @param mixed $creationDate
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }

    public function __construct()
    {
        parent::__construct();
        // your own logic
        $this->creationDate = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

}