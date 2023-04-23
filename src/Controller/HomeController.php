<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use App\Form\RecipeType;
use App\Service\GPT3_Service;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, GPT3_Service $openAi): Response
    {
        $finder = new Finder();
        $finder->files()->in('images')->name('*.png');
        $images = [];
        foreach ($finder as $file) {
            $images[] = $file->getFilename();
        }
        $random_image = $images ? $images[array_rand($images)] : '';

        $form = $this->createForm(RecipeType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $json = $openAi->getRecipe($data['instructions']);

            return $this->render('home/recipe.html.twig', [
                'json' => $json ?? null,
            ]);
        }
        return $this->render('home/index.html.twig', [
            'random_image' => $random_image,
            'form' => $form->createView(),
        ]);
    }
}
