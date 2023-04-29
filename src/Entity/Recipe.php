<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
class Recipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'recipe', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?ingredients $ingredients = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $recipe = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIngredients(): ?ingredients
    {
        return $this->ingredients;
    }

    public function setIngredients(ingredients $ingredients): self
    {
        $this->ingredients = $ingredients;

        return $this;
    }

    public function getRecipe(): ?string
    {
        return $this->recipe;
    }

    public function setRecipe(string $recipe): self
    {
        $this->recipe = $recipe;

        return $this;
    }
}
