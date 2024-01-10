<?php


namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="user")
 */
class User implements UserInterface
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
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    
    /**
    * @ORM\Column(type="string", length=255, nullable=true, options={"default": "uploads/user_images/avatar.png"})
    */
    private $image;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];


   // Add setEmail method
   public function setEmail(string $email): self
   {
       $this->email = $email;

       return $this;
   }

   // Add getEmail method
   public function getEmail(): string
   {
       return $this->email;
   }
   public function getUsername(): string
   {
       return (string) $this->email;
   }
    
   // Add setPassword method
   public function setPassword(string $password): self
   {
       $this->password = $password;

       return $this;
   }

   // Add getPassword method
   public function getPassword(): string
   {
       return $this->password;
   }

   // Add setName method
   public function setName(string $name): self
   {
       $this->name = $name;

       return $this;
   }

   // Add getName method
   public function getName(): string
   {
       return $this->name;
   }

   // Add setImage method
   public function setImage(string $image): self
   {
       $this->image = $image;

       return $this;
   }

   // Add getImage method
   public function getImage(): string
   {
       // If image is null, return the default avatar image path
       return $this->image ? 'uploads/user_images/' . $this->image : 'uploads/user_images/avatar.png';
   }

   public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

   // Add setId method
   public function setId(int $id): self
   {
       $this->id = $id;

       return $this;
   }

   // Add getId method
   public function getId(): int
   {
       return $this->id;
   }

   public function eraseCredentials()
   {

   }

   public function getSalt()
   {
   return null;
   }

 
}