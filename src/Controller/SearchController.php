<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\ChatLog;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function index(Security $security, ManagerRegistry $doctrine): Response
    {
        $finder = new Finder();
        $finder->files()->in('images')->name('*.png');
        $images = [];
        foreach ($finder as $file) {
            $images[] = $file->getFilename();
        }
        $random_image = $images ? $images[array_rand($images)] : '';

        $user = $security->getUser();
        $chatsLogs = null;

        if ($user instanceof User) {
            $chatsLogs = $user->getChatLogs();
        }

        return $this->render('search/index.html.twig', [
            'random_image' => $random_image,
            'user' => $user,
            'chatLogs' => $chatsLogs,
            'controller_name' => 'SearchController',
        ]);
    }
}
