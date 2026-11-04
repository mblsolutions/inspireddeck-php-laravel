<?php

namespace MBLSolutions\InspiredDeckLaravel\Tests\Unit;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use MBLSolutions\InspiredDeck\Authentication as InspiredDeckAuthentication;
use MBLSolutions\InspiredDeck\Exceptions\AuthenticationException;
use MBLSolutions\InspiredDeck\InspiredDeck;
use TypeError;
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
    public function check_that_authentication_can_return_false(): void
    {
        $authentication = new Authentication();

        $this->assertFalse($authentication->get());
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
    public function login_destroys_the_old_session_record(): void
    {
        $session = session()->driver();
        $session->put('pending_login', 'encrypted-value');
        $session->save();

        $oldId = $session->getId();
        $this->assertStringContainsString('encrypted-value', $session->getHandler()->read($oldId));

        $stub = $this->createMock(InspiredDeckAuthentication::class);
        $stub->method('password')->willReturn($this->tokenResponse());

        (new Authentication($stub))->login('john.doe@example.com', 'password');

        $this->assertNotSame($oldId, $session->getId());
        $this->assertSame('', $session->getHandler()->read($oldId));
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

        $authentication = new Authentication($this->createMock(InspiredDeckAuthentication::class));

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

    /** @test **/
    public function login_without_extra_fields_passes_an_empty_array(): void
    {
        $stub = $this->createMock(InspiredDeckAuthentication::class);

        $stub->expects($this->once())
             ->method('password')
             ->with(1, 'secret', 'john.doe@example.com', 'password', [])
             ->willReturn($this->tokenResponse());

        $this->assertTrue((new Authentication($stub))->login('john.doe@example.com', 'password'));
    }

    /** @test **/
    public function login_passes_extra_fields_on(): void
    {
        $stub = $this->createMock(InspiredDeckAuthentication::class);

        $stub->expects($this->once())
             ->method('password')
             ->with(1, 'secret', 'john.doe@example.com', 'password', ['otp' => '123456'])
             ->willReturn($this->tokenResponse());

        $this->assertTrue((new Authentication($stub))->login('john.doe@example.com', 'password', ['otp' => '123456']));
    }

    /** @test **/
    public function recovery_codes_are_returned_once_and_never_stored_in_the_session(): void
    {
        $codes = $this->recoveryCodes();

        $stub = $this->createMock(InspiredDeckAuthentication::class);
        $stub->method('password')->willReturn($this->tokenResponse(['recovery_codes' => $codes]));

        $authentication = new Authentication($stub);

        $this->assertTrue($authentication->login('john.doe@example.com', 'password', ['otp' => '123456']));

        $this->assertEquals($this->tokenResponse(), session()->get('inspireddeck_auth_session'));
        $this->assertSessionHasNoCodes($codes);

        $this->assertSame($codes, $authentication->pullRecoveryCodes());
        $this->assertSame([], $authentication->pullRecoveryCodes());
    }

    /** @test **/
    public function login_without_recovery_codes_gives_none(): void
    {
        $stub = $this->createMock(InspiredDeckAuthentication::class);
        $stub->method('password')->willReturn($this->tokenResponse());

        $authentication = new Authentication($stub);
        $authentication->login('john.doe@example.com', 'password');

        $this->assertSame([], $authentication->pullRecoveryCodes());
    }

    /** @test **/
    public function recovery_codes_that_are_not_a_list_are_ignored(): void
    {
        $stub = $this->createMock(InspiredDeckAuthentication::class);
        $stub->method('password')->willReturn($this->tokenResponse(['recovery_codes' => 'abcde-12345']));

        $authentication = new Authentication($stub);
        $authentication->login('john.doe@example.com', 'password');

        $this->assertSame([], $authentication->pullRecoveryCodes());
        $this->assertEquals($this->tokenResponse(), session()->get('inspireddeck_auth_session'));
        $this->assertSessionHasNoCodes(['abcde-12345']);
    }

    /** @test **/
    public function recovery_codes_can_be_pulled_once_after_a_role_error(): void
    {
        $codes = $this->recoveryCodes();

        $stub = $this->createMock(InspiredDeckAuthentication::class);
        $stub->method('password')->willReturn($this->tokenResponse(['recovery_codes' => $codes], 'store_operator'));

        $authentication = new Authentication($stub);

        try {
            $authentication->login('john.doe@example.com', 'password', ['otp' => '123456']);
            $this->fail('InvalidUserRoleException was not thrown');
        } catch (InvalidUserRoleException $exception) {
            // expected
        }

        $this->assertNull(session()->get('inspireddeck_auth_session'));
        $this->assertSessionHasNoCodes($codes);
        $this->assertSame($codes, $authentication->pullRecoveryCodes());
        $this->assertSame([], $authentication->pullRecoveryCodes());
    }

    /** @test **/
    public function failed_login_clears_recovery_codes_not_yet_pulled(): void
    {
        $stub = $this->createMock(InspiredDeckAuthentication::class);
        $stub->method('password')->will($this->onConsecutiveCalls(
            $this->tokenResponse(['recovery_codes' => $this->recoveryCodes()]),
            $this->throwException(new AuthenticationException('{"message":"Enter the code.","error":"two_factor_required"}', 422))
        ));

        $authentication = new Authentication($stub);
        $authentication->login('john.doe@example.com', 'password', ['otp' => '123456']);

        try {
            $authentication->login('john.doe@example.com', 'password');
            $this->fail('AuthenticationException was not thrown');
        } catch (AuthenticationException $exception) {
            // expected
        }

        $this->assertSame([], $authentication->pullRecoveryCodes());
    }

    /** @test **/
    public function second_login_clears_recovery_codes_not_yet_pulled(): void
    {
        $stub = $this->createMock(InspiredDeckAuthentication::class);
        $stub->method('password')->will($this->onConsecutiveCalls(
            $this->tokenResponse(['recovery_codes' => $this->recoveryCodes()]),
            $this->tokenResponse()
        ));

        $authentication = new Authentication($stub);
        $authentication->login('john.doe@example.com', 'password', ['otp' => '123456']);
        $authentication->login('john.doe@example.com', 'password');

        $this->assertSame([], $authentication->pullRecoveryCodes());
    }

    /** @test **/
    public function logout_ends_the_api_token_then_clears_the_session(): void
    {
        InspiredDeck::setToken('another-token');
        session()->put('inspireddeck_auth_session', $this->tokenResponse());

        $calls = [];

        $stub = $this->createMock(InspiredDeckAuthentication::class);
        $stub->expects($this->once())
             ->method('logout')
             ->willReturnCallback(function () use (&$calls) {
                 // Only record here: logout() catches everything, including failed asserts
                 $calls[] = [
                     'token' => InspiredDeck::getToken(),
                     'session' => session()->has('inspireddeck_auth_session'),
                 ];

                 return ['message' => 'Logged out'];
             });

        $this->assertTrue((new Authentication($stub))->logout());

        $this->assertSame([[
            'token' => $this->tokenResponse()['access_token'],
            'session' => true,
        ]], $calls);
        $this->assertNull(session()->get('inspireddeck_auth_session'));
    }

    /** @test **/
    public function logout_ignores_errors_from_the_api(): void
    {
        $errors = [
            new AuthenticationException('{"message":"Unauthenticated."}', 401),
            new ConnectException('Connection refused', new Request('POST', '/api/logout')),
            new TypeError('Unexpected response'),
        ];

        foreach ($errors as $error) {
            session()->put('inspireddeck_auth_session', $this->tokenResponse());

            $stub = $this->createMock(InspiredDeckAuthentication::class);
            $stub->expects($this->once())->method('logout')->willThrowException($error);

            $this->assertTrue((new Authentication($stub))->logout());
            $this->assertNull(session()->get('inspireddeck_auth_session'));
        }
    }

    /** @test **/
    public function logout_without_a_token_makes_no_api_call(): void
    {
        $stub = $this->createMock(InspiredDeckAuthentication::class);
        $stub->expects($this->never())->method('logout');

        $authentication = new Authentication($stub);

        $this->assertTrue($authentication->logout());

        session()->put('inspireddeck_auth_session', ['user' => ['role' => 'admin']]);

        $this->assertTrue($authentication->logout());
        $this->assertNull(session()->get('inspireddeck_auth_session'));
    }

    /**
     * A successful token response
     *
     * @param array $extra
     * @param string $role
     * @return array
     */
    private function tokenResponse(array $extra = [], string $role = 'programme_manager'): array
    {
        return array_merge([
            'token_type' => 'Bearer',
            'expires_in' => 31622400,
            'access_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjBmOGMwNDAxZDAy',
            'refresh_token' => 'def5020002eca9ac7875d5d800c195024d7fb702535c0d30a0',
            'user' => [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'role' => $role
            ],
            'api_version' => 'v1.0.0',
        ], $extra);
    }

    /**
     * Eight recovery codes
     *
     * @return array
     */
    private function recoveryCodes(): array
    {
        return [
            'a1b2c-d3e4f', 'g5h6i-j7k8l', 'm9n0o-p1q2r', 's3t4u-v5w6x',
            'y7z8a-b9c0d', 'e1f2g-h3i4j', 'k5l6m-n7o8p', 'q9r0s-t1u2v',
        ];
    }

    /**
     * Assert that no recovery code is anywhere in the session
     *
     * @param array $codes
     */
    private function assertSessionHasNoCodes(array $codes): void
    {
        $session = serialize(session()->all());

        $this->assertStringNotContainsString('recovery_codes', $session);

        foreach ($codes as $code) {
            $this->assertStringNotContainsString($code, $session);
        }
    }

}
