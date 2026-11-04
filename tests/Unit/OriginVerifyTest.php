<?php

namespace MBLSolutions\InspiredDeckLaravel\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MBLSolutions\InspiredDeck\Api\ApiRequestor;
use MBLSolutions\InspiredDeck\InspiredDeck;
use MBLSolutions\InspiredDeckLaravel\InspiredDeckServiceProvider;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;

class OriginVerifyTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [InspiredDeckServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('inspireddeck.origin_verify', bin2hex(random_bytes(32)));
    }

    protected function tearDown(): void
    {
        InspiredDeck::setOriginVerify(null);

        parent::tearDown();
    }

    #[Test]
    #[TestDox('Provider boot configures origin verification before any HTTP middleware or SDK singleton is resolved')]
    public function testOriginVerifyIsLoadedForConsoleAndHttpRequests(): void
    {
        $this->assertFalse($this->app->resolved(InspiredDeck::class));
        $mock = new MockHandler([new Response(200, [], '{}')]);
        $requestor = new ApiRequestor(new Client(['handler' => HandlerStack::create($mock)]));

        $requestor->getRequest('https://api.example.test/resource', [], []);

        $this->assertSame(
            config('inspireddeck.origin_verify'),
            $mock->getLastRequest()->getHeaderLine('X-Origin-Verify')
        );
    }

    #[Test]
    #[TestDox('Booting an application without a secret clears any previously configured origin header')]
    public function testOriginVerifyIsClearedWhenConfigurationIsMissing(): void
    {
        foreach ([null, ''] as $value) {
            config(['inspireddeck.origin_verify' => $value]);

            (new InspiredDeckServiceProvider($this->app))->boot();

            $mock = new MockHandler([new Response(200, [], '{}')]);
            $requestor = new ApiRequestor(new Client(['handler' => HandlerStack::create($mock)]));
            $requestor->getRequest('https://api.example.test/resource', [], []);

            $this->assertFalse($mock->getLastRequest()->hasHeader('X-Origin-Verify'));
        }
    }
}
