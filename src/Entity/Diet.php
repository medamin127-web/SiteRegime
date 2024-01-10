<?php

namespace App\Entity;



use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity(repositoryClass="App\Repository\DietRepository")
 */
class Diet
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $dietName;

    /**
     * @ORM\Column(type="integer")
     */
    private $duration;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     */
    private $category;

     /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    // Getter and setter methods


     /**
    * @ORM\OneToMany(targetEntity="PlatDiet", mappedBy="diet")
    */
    private $platDiets;

    public function __construct()
    {
        $this->platDiets = new ArrayCollection();
    }
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDietName(): ?string
    {
        return $this->dietName;
    }

    public function setDietName(string $dietName): self
    {
        $this->dietName = $dietName;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
         // Check if the provided value is a valid file
    if ($image instanceof UploadedFile) {
        // Handle the file upload and set the correct path
        $newFilename = $this->uploadImage($image);
        $this->image = $newFilename;
    } else {
        // If $image is not an instance of UploadedFile, set it directly
        $this->image = $image;
    }
    return $this;

    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

      /**
     * @return Collection|PlatDiet[]
     */
    public function getPlatDiets(): Collection
    {
        return $this->platDiets;
    }

     /**
     * @return $this
     */
    public function addPlatDiet(PlatDiet $platDiet): self
    {
        if (!$this->platDiets->contains($platDiet)) {
            $this->platDiets[] = $platDiet;
            $platDiet->setDiet($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removePlatDiet(PlatDiet $platDiet): self
    {
        if ($this->platDiets->contains($platDiet)) {
            $this->platDiets->removeElement($platDiet);
            // set the owning side to null (unless already changed)
            if ($platDiet->getDiet() === $this) {
                $platDiet->setDiet(null);
            }
        }

        return $this;
    }


    public function getTotalCalories(): int
    {
        $totalCalories = 0;

        foreach ($this->platDiets as $platDiet) {
            $totalCalories += $platDiet->getPlat()->getNbrCalories();
        }

        return $totalCalories;
    }
}
