<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 */
class Article
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
    private $articleName;

    /**
     * @ORM\Column(type="text")
     */
    private $articleDescription;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $articleImage;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    // Getter and setter for $id
    public function getId(): ?int
    {
        return $this->id;
    }

    // No setter for $id as it is auto-generated

    // Getter and setter for $articleName
    public function getArticleName(): ?string
    {
        return $this->articleName;
    }

    public function setArticleName(string $articleName): self
    {
        $this->articleName = $articleName;

        return $this;
    }

    // Getter and setter for $articleDescription
    public function getArticleDescription(): ?string
    {
        return $this->articleDescription;
    }

    public function setArticleDescription(string $articleDescription): self
    {
        $this->articleDescription = $articleDescription;

        return $this;
    }

    // Getter and setter for $content
    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    // Getter and setter for $articleImage
    public function getArticleImage(): ?string
    {
        return $this->articleImage;
    }

    public function setArticleImage(string $articleImage): self
    {
        $this->articleImage = $articleImage;

        return $this;
    }

    // Getter and setter for $date
    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}