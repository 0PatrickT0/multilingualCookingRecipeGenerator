<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Recipe;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function index (Security $security, ManagerRegistry $doctrine): Response
    {
        $finder = new Finder();
        $finder->files()->in('images')->name('*.png');
        $images = [];
        foreach ($finder as $file) {
            $images[] = $file->getFilename();
        }
        $random_image = $images ? $images[array_rand($images)] : '';

        $recipes = $doctrine->getRepository(Recipe::class)->findBy(['user' => $this->getUser()]);

        return $this->render('search/index.html.twig', [
            'random_image' => $random_image,
            'recipes' => $recipes,
            'user' => $security->getUser(),
            'controller_name' => 'SearchController',
        ]);
    }
}
