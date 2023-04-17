<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Finder\Finder;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index_picture(): Response
    {
        $finder = new Finder();
        $finder->files()->in('images')->name('*.png');
        $images = [];
        foreach ($finder as $file) {
            $images[] = $file->getFilename();
        }
        $random_image = $images ? $images[array_rand($images)] : '';
        return $this->render('home/index.html.twig', [
            'random_image' => $random_image,
        ]);
/*         return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]); */
    }
}
