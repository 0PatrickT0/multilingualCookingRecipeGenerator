<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormFactoryInterface;

class RegistrationController extends AbstractController
{
    #[Route('/registration', name: 'app_registration')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, ValidatorInterface $validator, FormFactoryInterface $formFactory): Response
    {
        $finder = new Finder();
        $finder->files()->in('images')->name('*.png');
        $images = [];
        foreach ($finder as $file) {
            $images[] = $file->getFilename();
        }
        $random_image = $images ? $images[array_rand($images)] : '';

        $user = new User();
        $form = $formFactory->createBuilder()
            ->add('username', TextType::class)
            ->add('password', PasswordType::class)
            ->add('password_confirm', PasswordType::class)
            ->add('action', HiddenType::class)
            ->add('Register', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);


        $username = $request->request->get('username');
        $password = $request->request->get('password');

        if ($username && $password) {

            $user = new User();
            $user->setUsername($username);
            $user->setRoles([
                'ROLE_USER',
            ]);

            // hash the password
            $hashedPassword = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            $errors = $validator->validate($user);
            if (count($errors) > 0) {
                return new Response((string) $errors, 400);
            }
            return $this->redirectToRoute('app_login');
        }
        return $this->render('registration/index.html.twig', [
            'random_image' => $random_image,
            'form' => $form->createView(),
            'controller_name' => 'RegistrationController',
        ]);
    }
}
