<?php

namespace App\Entity;

use App\Repository\FavoriteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FavoriteRepository::class)
 */
class Favorite
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="favorites")
     */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity=Recipe::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $recipes;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsers(): ?User
    {
        return $this->users;
    }

    public function setUsers(?User $users): self
    {
        $this->users = $users;

        return $this;
    }

    public function getRecipes(): ?Recipe
    {
        return $this->recipes;
    }

    public function setRecipes(?Recipe $recipes): self
    {
        $this->recipes = $recipes;

        return $this;
    }
}
