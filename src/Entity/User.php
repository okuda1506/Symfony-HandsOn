<?php


namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("username")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $username;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;


    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getUsername(): ?string
    {
        return $this->username;
    }


    public function setUsername(string $username): self
    {
        $this->username = $username;


        return $this;
    }


    public function getPassword(): ?string
    {
        return $this->password;
    }


    public function setPassword(string $password): self
    {
        $this->password = $password;


        return $this;
    }


    public function getEmail(): ?string
    {
        return $this->email;
    }


    public function setEmail(string $email): self
    {
        $this->email = $email;


        return $this;
    }


    public function isActive(): ?bool
    {
        return $this->isActive;
    }


    public function setActive(bool $isActive): self
    {
        $this->isActive = $isActive;


        return $this;
    }


    public function __construct()
    {
        $this->isActive = true;
    }


    public function getSalt()
    {
        return null;
    }


    public function getRoles()
    {
        if ($this->username == 'admin') {
            return array('ROLE_ADMIN');
        } else {
            return array('ROLE_USER');
        }
        
    }


    public function eraseCredentials()
    {
    }


    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->isActive,
        ));
    }


    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->isActive,
        ) = unserialize($serialized, array('allowed_classes' => false));
    }    
}