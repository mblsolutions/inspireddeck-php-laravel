<?php

namespace MBLSolutions\InspiredDeckLaravel\Tests\Unit;

use MBLSolutions\InspiredDeckLaravel\InspiredDeckAuth;
use MBLSolutions\InspiredDeckLaravel\InspiredDeckAuthentication;
use MBLSolutions\InspiredDeckLaravel\Tests\LaravelTestCase;
use Mockery;

class InspiredDeckAuthenticationTest extends LaravelTestCase
{

    /** @test **/
    public function can_get_users_name(): void
    {
        $auth = Mockery::mock(InspiredDeckAuth::class);

        $auth->shouldReceive('get')
            ->andReturn([
                'user' => [
                    'name' => 'John Doe'
                ]
            ]);

        $authentication = new InspiredDeckAuthentication($auth);

        $this->assertEquals('John Doe', $authentication->getName());
    }

    /** @test **/
    public function can_get_users_email(): void
    {
        $auth = Mockery::mock(InspiredDeckAuth::class);

        $auth->shouldReceive('get')
            ->andReturn([
                'user' => [
                    'email' => 'john.doe@example.com'
                ]
            ]);

        $authentication = new InspiredDeckAuthentication($auth);

        $this->assertEquals('john.doe@example.com', $authentication->getEmail());
    }

    /** @test * */
    public function can_get_users_role(): void
    {
        $auth = Mockery::mock(InspiredDeckAuth::class);

        $auth->shouldReceive('get')
            ->andReturn([
                'user' => [
                    'role' => 'programme_manager'
                ]
            ]);

        $authentication = new InspiredDeckAuthentication($auth);

        $this->assertEquals('programme_manager', $authentication->getRole());
    }

    /** @test * */
    public function can_check_user_has_role(): void
    {
        $auth = Mockery::mock(InspiredDeckAuth::class);

        $auth->shouldReceive('get')
            ->andReturn([
                'user' => [
                    'role' => 'programme_manager'
                ]
            ]);

        $authentication = new InspiredDeckAuthentication($auth);

        $this->assertTrue($authentication->hasRole('programme_manager'));
        $this->assertFalse($authentication->hasRole('customer_service_manager'));
    }

    /** @test * */
    public function can_check_if_user_has_roles(): void
    {
        $auth = Mockery::mock(InspiredDeckAuth::class);

        $auth->shouldReceive('get')
            ->andReturn([
                'user' => [
                    'role' => 'customer_service_manager'
                ]
            ]);

        $authentication = new InspiredDeckAuthentication($auth);

        $this->assertTrue($authentication->hasRoles([
            'programme_manager',
            'customer_service_manager'
        ]));

        $this->assertFalse($authentication->hasRoles([
            'programme_manager',
            'report'
        ]));
    }

    /** @test * */
    public function can_get_users_brand(): void
    {
        $auth = Mockery::mock(InspiredDeckAuth::class);

        $auth->shouldReceive('get')
            ->andReturn([
                'user' => [
                    'name' => 'John Doe',
                    'role' => 'programme_manager',
                    'brand' => [
                        'name' => 'acme inc',
                    ]
                ],
            ]);

        $authentication = new InspiredDeckAuthentication($auth);


        self::assertIsArray($authentication->getBrand());
        self::assertEquals('acme inc', $authentication->getBrand()['name']);
    }

    /** @test */
    public function can_get_users_display_name()
    {
        $auth = Mockery::mock(InspiredDeckAuth::class);

        $auth->shouldReceive('get')
            ->andReturn([
                'user' => [
                    'name' => 'John Doe',
                    'role' => 'programme_manager',
                    'brand' => [
                        'name' => 'Acme Inc',
                    ]
                ],
            ]);

        $authentication = new InspiredDeckAuthentication($auth);

        self::assertIsArray($authentication->getBrand());
        self::assertEquals('Acme Inc | John Doe', $authentication->getDisplayName());
    }

    /** @test */
    public function display_names_without_brand_display_only_name()
    {
        $auth = Mockery::mock(InspiredDeckAuth::class);

        $auth->shouldReceive('get')
            ->andReturn([
                'user' => [
                    'name' => 'John Doe',
                    'role' => 'programme_manager',
                    'brand' => null,
                ],
            ]);

        $authentication = new InspiredDeckAuthentication($auth);

        self::assertEquals('John Doe', $authentication->getDisplayName());
    }

}