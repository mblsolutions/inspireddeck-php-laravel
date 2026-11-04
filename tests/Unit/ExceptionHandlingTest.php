<?php

namespace MBLSolutions\InspiredDeckLaravel\Tests\Unit;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use MBLSolutions\InspiredDeck\Exceptions\AuthenticationException;
use MBLSolutions\InspiredDeckLaravel\Exceptions\AuthenticationException as LaravelAuthenticationException;
use MBLSolutions\InspiredDeckLaravel\InspiredDeckServiceProvider;
use MBLSolutions\InspiredDeckLaravel\Tests\LaravelTestCase;

require_once __DIR__ . '/../Stubs/helpers.php';

class ExceptionHandlingTest extends LaravelTestCase
{

    /** {@inheritdoc} **/
    protected function setUp(): void
    {
        parent::setUp();

        Route::get('login', function () {
            return 'login';
        })->name('login');

        app('router')->getRoutes()->refreshNameLookups();

        session()->put('inspireddeck_auth_session', ['access_token' => 'token']);
    }

    /** @test **/
    public function api_401_clears_the_session_and_goes_to_login(): void
    {
        $response = InspiredDeckServiceProvider::exceptionHandling(
            request(),
            new AuthenticationException('{"message":"Unauthenticated."}', 401),
            function () {
                return 'fallback';
            }
        );

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(url('login'), $response->getTargetUrl());
        $this->assertSame('Please login to proceed.', session('errors')->first());
        $this->assertNull(session()->get('inspireddeck_auth_session'));
    }

    /** @test **/
    public function other_api_authentication_errors_are_left_to_the_app(): void
    {
        $response = InspiredDeckServiceProvider::exceptionHandling(
            request(),
            new AuthenticationException('{"message":"Too Many Attempts."}', 429),
            function () {
                return 'fallback';
            }
        );

        $this->assertSame('fallback', $response);
        $this->assertSame(['access_token' => 'token'], session()->get('inspireddeck_auth_session'));
    }

    /** @test **/
    public function middleware_401_still_goes_to_login(): void
    {
        $response = InspiredDeckServiceProvider::exceptionHandling(
            request(),
            new LaravelAuthenticationException(401),
            function () {
                return 'fallback';
            }
        );

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(url('login'), $response->getTargetUrl());
        $this->assertSame('Please login to proceed.', session('errors')->first());
    }

}
