<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use GuzzleHttp\Client;

final class MailerClient
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'http://mailer:8025',
        ]);
    }

    public function clear(): void
    {
        $this->client->delete('/api/v1/messages');
    }

    public function hasEmailSentTo(string $email): bool
    {
        $response = $this->client->get('/api/v2/search?kind=to&query='.urlencode($email));
        $data = Json::decode($response->getBody()->getContents());

        return $data['total'] > 0;
    }
}
