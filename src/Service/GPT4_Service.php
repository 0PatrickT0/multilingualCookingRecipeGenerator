<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GPT4_Service
{
    private $client;
    private $parameterBag;

    public function __construct(HttpClientInterface $client, ParameterBagInterface $parameterBag)
    {
        $this->client = $client;
        $this->parameterBag = $parameterBag;
    }

    public function getRecipeByGPT4(string $instructions): string
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
                    'model' => 'gpt-4-0314',
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

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('An error has occurred with code: ' . $response->getStatusCode() . ', please try again.');
        }

        $responseArray = $response->toArray() ?? [];
        return nl2br($responseArray['choices'][0]['message']['content']);
    }
}