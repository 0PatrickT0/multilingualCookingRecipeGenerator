<?php

namespace App\Service;

use App\Entity\ChatLog;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

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

        try {
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
                                'content' => "Initial context: You provide healthy and balanced recipes based on the ingredients provided in the instructions.
                                
                                Instructions: Please provide a recipe using the following ingredients: $instructions.
                                
                                Limitations: Please respond strictly in the same language as the ingredients provided in the instructions. Make sure to include necessary details for each step of the recipe.
                                
                                Example:
                                
                                Recipe name:
                                
                                Ingredients:
                                
                                Instructions:
                                
                                Response structure:
                                Strictly in the same language as the ingredients provided in the instructions.
                                Recipe name:
                                Ingredients:
                                Preparation instructions:
                                Finish by wishing a good appetite.",
                            ]
                        ],
                    ],
                ]
            );

            switch ($response->getStatusCode()) {
                case 200:
                    $responseArray = $response->toArray() ?? [];

                    // Create ChatLog entity and set the question and answer
                    $chatLog = new ChatLog();
                    $chatLog->setQuestion($instructions);
                    $chatLog->setAnswer($responseArray['choices'][0]['message']['content']);

                    // Link ChatLog to User if user is connected
                    $user = $this->security->getUser();
                    if ($user) {
                        $chatLog->setUser($user);
                    } else {
                        $anonymousUser = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'anonymous']);
                        $chatLog->setUser($anonymousUser);
                    }

                    $this->entityManager->persist($chatLog);
                    $this->entityManager->flush();

                    return nl2br($responseArray['choices'][0]['message']['content']);

                    break;

                default:
                    return 'An error has occurred with code: ' . $response->getStatusCode() . ', please try again.';
                    break;
            }
        } catch (TransportExceptionInterface $exception) {
            // Handle transport exception
            return 'An error has occurred: ' . $exception->getMessage();
        }
    }
}
