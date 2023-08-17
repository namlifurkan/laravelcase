<?php

namespace App\Services;

use GuzzleHttp\Exception\GuzzleException;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;
use GuzzleHttp\Client as GuzzleClient;

class SmsService
{
    private string $sid;
    private string $token;
    private Client $client;


    /**
     * @throws ConfigurationException
     */
    public function __construct()
    {
        $this->sid = "AC8450b5db3a8484df19e37fe40b31257a";
        $this->token = "40a1e15fe4b9f63d1ee965b07c4a6c2a";
        $this->client = new Client($this->sid, $this->token);
    }

    /**
     * @throws ConfigurationException
     * @throws TwilioException
     */
    public function sendSms($message): void
    {
        $response = $this->client->messages
            ->create("+15005550010", // to
                [
                    "body" => $message,
                    "from" => "+15005550006",
                ]
            );
    }

    /**
     * @throws GuzzleException
     */
    public function getInfo($uri): void
    {
        $client = new GuzzleClient();

        $response = $client->request('GET', $uri, [
            'auth' => [$this->sid, $this->token]
        ]);

        $decodedResponse = json_decode($response->getBody(), true);

        var_dump($decodedResponse);

    }
}
