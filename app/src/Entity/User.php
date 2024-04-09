<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'First name cannot be blank')]
    #[Assert\Length(max: 255, maxMessage: 'First name is too long')]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'Last name cannot be blank')]
    #[Assert\Length(max: 255, maxMessage: 'Last name is too long')]
    private ?string $lastname = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: 'Address is too long')]
    private ?string $address = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = htmlspecialchars(trim($firstname), ENT_QUOTES, 'UTF-8');

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = htmlspecialchars(trim($lastname), ENT_QUOTES, 'UTF-8');

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = htmlspecialchars(trim($address), ENT_QUOTES, 'UTF-8');

        return $this;
    }

    public function getFullname(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }
}
