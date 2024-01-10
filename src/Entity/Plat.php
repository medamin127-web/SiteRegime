<?php

namespace App\Entity;

use App\Repository\PlatRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlatRepository::class)
 */
class Plat
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomplat;

    /**
     * @ORM\Column(type="float")
     */
    private $cout;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbrCalories;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ingredients;



     /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

/**
 * @ORM\OneToMany(targetEntity="PlatDiet", mappedBy="plat")
 */
    private $platDiets;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomplat(): ?string
    {
        return $this->nomplat;
    }

    public function setNomplat(string $nomplat): self
    {
        $this->nomplat = $nomplat;

        return $this;
    }

    public function getCout(): ?float
    {
        return $this->cout;
    }

    public function setCout(float $cout): self
    {
        $this->cout = $cout;

        return $this;
    }

    public function getNbrCalories(): ?int
    {
        return $this->nbrCalories;
    }

    public function setNbrCalories(?int $nbrCalories): self
    {
        $this->nbrCalories = $nbrCalories;

        return $this;
    }

    public function getIngredients(): ?string
    {
        return $this->ingredients;
    }

    public function setIngredients(string $ingredients): self
    {
        $this->ingredients = $ingredients;

        return $this;
    }
    
    // Add getImage method
    public function getImage(): string
    {
        return $this->image;
    }
    
   // Add setImage method
   public function setImage(string $image): self
   {
       $this->image = $image;

       return $this;
   }

}
