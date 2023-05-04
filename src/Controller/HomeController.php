<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use App\Form\RecipeType;
use App\Service\GPT3_5_Service;
use Symfony\Bundle\SecurityBundle\Security;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, GPT3_5_Service $openAi, Security $security): Response
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
            $json = $openAi->getRecipeByGPT3_5($data['instructions']);

            return $this->render('home/recipe.html.twig', [
                'json' => $json ?? null,
            ]);
        }
        return $this->render('home/index.html.twig', [
            'random_image' => $random_image,
            'form' => $form->createView(),
            'user' => $security->getUser(),
        ]);
    }
}
