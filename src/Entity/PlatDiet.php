<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="plat_diet")
 */
class PlatDiet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Plat")
     * @ORM\JoinColumn(name="plat_id", referencedColumnName="id", nullable=false)
     */
    private $plat;

    /**
     * @ORM\ManyToOne(targetEntity="Diet")
     * @ORM\JoinColumn(name="diet_id", referencedColumnName="id", nullable=false)
     */
    private $diet;

    // Add any additional fields or methods as needed

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of plat
     */
    public function getPlat()
    {
        return $this->plat;
    }

    /**
     * Set the value of plat
     *
     * @return self
     */
    public function setPlat($plat)
    {
        $this->plat = $plat;

        return $this;
    }

    /**
     * Get the value of diet
     */
    public function getDiet()
    {
        return $this->diet;
    }

    /**
     * Set the value of diet
     *
     * @return self
     */
    public function setDiet($diet)
    {
        $this->diet = $diet;

        return $this;
    }

    // Add any additional getter and setter methods or other functionality as needed
}
