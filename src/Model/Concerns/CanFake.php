<?php

namespace MBLSolutions\InspiredDeckLaravel\Model\Concerns;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MBLSolutions\InspiredDeck\Api\ApiRequestor;

trait CanFake
{
    /** @var bool $fake */
    protected static $fake = false;

    /** @var null|Response $mockedResponse */
    private $mockedResponse;

    /**
     * Set Model to fake HTTP calls
     */
    public static function fake()
    {
        self::$fake = true;
    }

    /**
     * Check if Model is Faking
     *
     * @return bool
     */
    public static function isFaking(): bool
    {
        return self::$fake;
    }

    /**
     * Set a Fake Response
     *
     * @param array $response
     * @param int $code
     * @param array|null $headers
     */
    public function setFakeResponse(array $response, int $code = 200, array $headers = null)
    {
        $this->mockedResponse = new Response(
            $code,
            $headers ?? ['Content-Type' => 'application/json'],
            json_encode($response)
        );
    }

    /**
     * Get Fake Response
     *
     * @return Response|null
     */
    public function getFakeResponse()
    {
        return $this->mockedResponse;
    }

    /**
     * Mock Expected HTTP Response
     *
     * @return null
     */
    protected function mockExpectedHttpResponse()
    {
        if ($this->mockedResponse === null) {
            $this->setFakeResponse([]);
        }

        $mock = new MockHandler([
            $this->mockedResponse
        ]);

        $client = new Client([
            'handler' => HandlerStack::create($mock)
        ]);

        ApiRequestor::setHttpClient($client);
    }
    
}