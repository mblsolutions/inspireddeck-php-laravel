<?php

namespace MBLSolutions\InspiredDeckLaravel\Tests\Unit;

use MBLSolutions\InspiredDeck\Authentication as InspiredDeckAuthentication;
use MBLSolutions\InspiredDeckLaravel\Authentication;
use MBLSolutions\InspiredDeckLaravel\Exceptions\InvalidUserRoleException;
use MBLSolutions\InspiredDeckLaravel\Tests\LaravelTestCase;

class AuthenticationTest extends LaravelTestCase
{

    /** @test **/
    public function can_authenticate(): void
    {
        $stub = $this->createMock(InspiredDeckAuthentication::class);

        $stub->method('password')
             ->willReturn([
                 'token_type' => 'Bearer',
                 'expires_in' => 31622400,
                 'access_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjBmOGMwNDAxZDAy',
                 'refresh_token' => 'def5020002eca9ac7875d5d800c195024d7fb702535c0d30a0',
                 'user' => [
                     'name' => 'John Doe',
                     'email' => 'john.doe@example.com',
                     'role' => 'programme_manager'
                 ],
                 'api_version' => 'v1.0.0',
             ]);

        $authentication = new Authentication($stub);

        $response = $authentication->login('john.doe@example.com', 'password');

        $this->assertTrue($response);
    }

    /** @test **/
    public function login_stores_authentication_in_session(): void
    {
        $response = [
            'token_type' => 'Bearer',
            'expires_in' => 31622400,
            'access_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjBmOGMwNDAxZDAy',
            'refresh_token' => 'def5020002eca9ac7875d5d800c195024d7fb702535c0d30a0',
            'user' => [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'role' => 'programme_manager'
            ],
            'api_version' => 'v1.0.0',
        ];

        $stub = $this->createMock(InspiredDeckAuthentication::class);

        $stub->method('password')
             ->willReturn($response);

        $authentication = new Authentication($stub);

        $authentication->login('john.doe@example.com', 'password');

        $this->assertEquals($response, session()->get('inspireddeck_auth_session'));
    }

    /** @test **/
    public function login_regenerates_session_id(): void
    {
        $originalSession = session()->getId();

        $response = [
            'token_type' => 'Bearer',
            'expires_in' => 31622400,
            'access_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjBmOGMwNDAxZDAy',
            'refresh_token' => 'def5020002eca9ac7875d5d800c195024d7fb702535c0d30a0',
            'user' => [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'role' => 'programme_manager'
            ],
            'api_version' => 'v1.0.0',
        ];

        $stub = $this->createMock(InspiredDeckAuthentication::class);

        $stub->method('password')
            ->willReturn($response);

        $authentication = new Authentication($stub);

        $authentication->login('john.doe@example.com', 'password');

        $this->assertNotSame(session()->getId(), $originalSession);
    }

    /** @test **/
    public function login_checks_for_valid_role(): void
    {
        $this->expectException(InvalidUserRoleException::class);

        $response = [
            'token_type' => 'Bearer',
            'expires_in' => 31622400,
            'access_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjBmOGMwNDAxZDAy',
            'refresh_token' => 'def5020002eca9ac7875d5d800c195024d7fb702535c0d30a0',
            'user' => [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'role' => 'store_operator'
            ],
            'api_version' => 'v1.0.0',
        ];

        $stub = $this->createMock(InspiredDeckAuthentication::class);

        $stub->method('password')
             ->willReturn($response);

        $authentication = new Authentication($stub);

        $authentication->login('john.doe@example.com', 'password');
    }

    /** @test */
    public function logout_removes_authenticated_session_information(): void
    {
        $response = [
            'token_type' => 'Bearer',
            'expires_in' => 31622400,
            'access_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjBmOGMwNDAxZDAy',
            'refresh_token' => 'def5020002eca9ac7875d5d800c195024d7fb702535c0d30a0',
            'user' => [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'role' => 'programme_manager'
            ],
            'api_version' => 'v1.0.0',
        ];

        session()->put('inspireddeck_auth_session', $response);

        $authentication = new Authentication();

        $authentication->logout();

        $this->assertNull(session()->get('inspireddeck_auth_session'));
    }

    /** @test **/
    public function can_check_if_authentication_session_exists(): void
    {
        $response = [
            'token_type' => 'Bearer',
            'expires_in' => 31622400,
            'access_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjBmOGMwNDAxZDAy',
            'refresh_token' => 'def5020002eca9ac7875d5d800c195024d7fb702535c0d30a0',
            'user' => [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'role' => 'programme_manager'
            ],
            'api_version' => 'v1.0.0',
        ];

        session()->put('inspireddeck_auth_session', $response);

        $authentication = new Authentication();

        $this->assertTrue($authentication->isAuthenticated());
    }

    /** @test **/
    public function authentication_must_have_a_valid_structure(): void
    {
        $response = [
            'success' => true
        ];

        session()->put('inspireddeck_auth_session', $response);

        $authentication = new Authentication();

        $this->assertFalse($authentication->isAuthenticated());
    }

}