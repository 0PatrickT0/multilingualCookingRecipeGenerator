<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GPT3_5_Service
{
    private $client;
    private $parameterBag;

    public function __construct(HttpClientInterface $client, ParameterBagInterface $parameterBag)
    {
        $this->client = $client;
        $this->parameterBag = $parameterBag;
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
                ],
                'json' => [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => "proposes une recette de cuisine dans la langue utilisée et avec les ingrédients suivant : " . $instructions,
                        ]
                    ],
                ],
                'auth_bearer' => $open_ai_key,
            ]
        );

        switch ($response->getStatusCode()) {
            case 200:
                $responseArray = $response->toArray() ?? [];
                return nl2br($responseArray['choices'][0]['message']['content']);
                break;

            default:
                return 'An error has occurred with code: ' . $response->getStatusCode() . ', please try again.';
                break;
        }
    }
}