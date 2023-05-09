<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Repository\IngredientsRepository;
use App\Repository\RecipeRepository;
use Symfony\Bundle\SecurityBundle\Security;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function index(IngredientsRepository $ingredientsRepo, RecipeRepository $recipeRepo, Security $security): Response
    {
        $finder = new Finder();
        $finder->files()->in('images')->name('*.png');
        $images = [];
        foreach ($finder as $file) {
            $images[] = $file->getFilename();
        }
        $random_image = $images ? $images[array_rand($images)] : '';

        /*         $ingredients = $this->getDoctrine()
            ->getRepository(Ingredient::class)
            ->findAll(); */

        /* $recipes = $this->getDoctrine()
            ->getRepository(Recipe::class)
            ->findAll(); */

        /*         $ingredients = $ingredientsRepo->findAll();
        $recipes = $recipeRepo->findAll(); */

        return $this->render('search/index.html.twig', [
            'random_image' => $random_image,
            /* 'ingredients' => $ingredients, */
            /* 'recipes' => $recipes->getRecipe(), */
            'user' => $security->getUser(),
            'controller_name' => 'SearchController',
        ]);
    }
}
