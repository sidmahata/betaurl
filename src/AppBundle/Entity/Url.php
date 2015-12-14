<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Url
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UrlRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Url
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="longurl", type="text")
     */
    private $longurl;

    /**
     * @var integer
     *
     * @ORM\Column(name="longurlindex", type="bigint")
     */
    private $longurlindex;

    /**
     * created Time/Date
     *
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * updated Time/Date
     *
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    protected $updatedAt;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set longurl
     *
     * @param string $longurl
     * @return Url
     */
    public function setLongurl($longurl)
    {
        $this->longurl = $longurl;

        return $this;
    }

    /**
     * Get longurl
     *
     * @return string 
     */
    public function getLongurl()
    {
        return $this->longurl;
    }

    /**
     * Set longurlindex
     *
     * @param integer $longurlindex
     * @return Url
     */
    public function setlongurlindex($longurlindex)
    {
        $this->longurlindex = $longurlindex;

        return $this;
    }

    /**
     * Get longurlindex
     *
     * @return integer 
     */
    public function getlongurlindex()
    {
        return $this->longurlindex;
    }

    /**
     * Set createdAt
     *
     * @ORM\PrePersist
     */
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @ORM\PreUpdate
     */
    public function setUpdatedAt()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
