<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GPT3_Service
{
    private $client;
    private $parameterBag;

    public function __construct(HttpClientInterface $client, ParameterBagInterface $parameterBag)
    {
        $this->client = $client;
        $this->parameterBag = $parameterBag;
    }

    public function getRecipe(string $instructions): string
    {
        $open_ai_key = $this->parameterBag->get('OPENAI_API_KEY');

        $response = $this->client->request(
            'POST',
            'https://api.openai.com/v1/completions',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'text-davinci-003',
                    'prompt' => "proposes une recette de cuisine dans la langue utilisée et avec les ingrédients suivant : ".$instructions,
                    'temperature' => 0,
                    'max_tokens' => 4000,
                    'frequency_penalty' => 0.5,
                    'presence_penalty' => 0,
                ],
                'auth_bearer' => $open_ai_key,
            ]
        );

        switch ($response->getStatusCode()) {
            case 200:
                $responseArray = $response->toArray() ?? [];
                return nl2br($responseArray['choices'][0]['text']);
                break;

            default:
                return 'An error has occurred, please try again.';
                break;
        }
    }
}