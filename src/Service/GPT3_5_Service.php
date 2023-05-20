<?php

namespace App\Service;

use App\Entity\Ingredients;
use App\Entity\Recipe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\SecurityBundle\Security;

class GPT3_5_Service
{
    private $client;
    private $parameterBag;
    private $entityManager;
    private $security;

    public function __construct(HttpClientInterface $client, ParameterBagInterface $parameterBag, EntityManagerInterface $entityManager, Security $security)
    {
        $this->client = $client;
        $this->parameterBag = $parameterBag;
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function getRecipeByGPT3_5(string $instructions): string
    {
        $open_ai_key = $this->parameterBag->get('OPENAI_API_KEY');

        $response = $this->client->request(
            'POST',
            'https://api.openai.com/v1/chat/completions',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $open_ai_key,
                ],
                'json' => [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => "proposes une recette de cuisine dans la langue utilisée et avec les ingrédients suivants : " . $instructions,
                        ]
                    ],
                ],
            ]
        );

        switch ($response->getStatusCode()) {
            case 200:
                $responseArray = $response->toArray() ?? [];

                // Create Ingredients entity, set the ingredients and link it to User
                $ingredients = new Ingredients();
                $ingredients->setIngredients($instructions);

                // Link Ingredients to User if user is connected
                $user = $this->security->getUser();
                if ($user) {
                    $ingredients->setUser($user);
                }

                $this->entityManager->persist($ingredients);

                // Create Recipe entity, set the recipe text and link it to Ingredients
                $recipe = new Recipe();
                $recipe->setRecipe($responseArray['choices'][0]['message']['content']);
                $recipe->setIngredients($ingredients);
                $this->entityManager->persist($recipe);

                $this->entityManager->flush();

                return nl2br($responseArray['choices'][0]['message']['content']);

                break;

            default:
                return 'An error has occurred with code: ' . $response->getStatusCode() . ', please try again.';
                break;
        }
    }
}
