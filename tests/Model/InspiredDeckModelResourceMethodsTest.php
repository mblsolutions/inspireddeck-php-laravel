<?php

namespace MBLSolutions\InspiredDeckLaravel\Tests\Model;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use MBLSolutions\InspiredDeckLaravel\Tests\Stubs\Brand;
use MBLSolutions\InspiredDeckLaravel\Tests\TestCase;

class InspiredDeckModelResourceMethodsTest extends TestCase
{
    /** @var Brand $brand */
    protected $brand;

    /** {@inheritdoc} **/
    public function setUp()
    {
        parent::setUp();

        $this->brand = new Brand();

        $this->brand::fake();
    }

    /** @test */
    public function can_access_resource_method_that_returns_collection()
    {
        $this->brand->setFakeResponse([
            'data' => [
                [
                    'id' => 1,
                    'name' => 'Test Brand 1',
                    'active' => true
                ],
                [
                    'id' => 2,
                    'name' => 'Test Brand 2',
                    'active' => true
                ]
            ]
        ]);

        $brands = $this->brand->select();

        $this->assertInstanceOf(Collection::class, $brands);
        $this->assertInstanceOf(Brand::class, $brands->first());
        $this->assertEquals([
            'id' => 1,
            'name' => 'Test Brand 1',
            'active' => true
        ], $brands->first()->toArray());
    }

    /** @test **/
    public function can_access_resource_method_show()
    {
        $this->brand->setFakeResponse([
            'data' => [
                'id' => 1,
                'name' => 'Test Brand',
                'active' => true
            ]
        ]);

        $brand = $this->brand->show(1);

        $this->assertInstanceOf(Brand::class, $brand);
        $this->assertEquals([
            'id' => 1,
            'name' => 'Test Brand',
            'active' => true
        ], $brand->toArray());
    }

    /** @test **/
    public function can_access_resource_method_all()
    {
        $this->brand->setFakeResponse([
            'data' => [
                [
                    'id' => 1,
                    'name' => 'Test Brand 1',
                    'active' => true
                ],
                [
                    'id' => 2,
                    'name' => 'Test Brand 2',
                    'active' => true
                ]
            ],
            'links' => [
                'first' => 'http://localhost?page=1',
                'last' => 'http://localhost?page=1',
                'prev' => null,
                'next' => null
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 1,
                'path' => 'http://localhost',
                'per_page' => 20,
                'to' => 1,
                'total' => 1
            ]
        ]);

        $brands = $this->brand->all();

        $this->assertInstanceOf(LengthAwarePaginator::class, $brands);
    }

}