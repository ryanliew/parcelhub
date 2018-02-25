<?php

namespace Tests\Unit;

use App\Lot;
use App\Product;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductValidatorTest extends TestCase
{
    private $user;

    public function setUp()
    {
        parent::setUp();

        $this->artisan('migrate');

        //Mock authenticated user
        $this->user = factory(User::class)->create(['name' => 'test_user']);

        $this->actingAs($this->user);
    }


    public function test_whenOutboundProductsNotExistInRequestParameter_shouldFail()
    {
        $rule = [
            'outbound_products' => 'required'
        ];

        $data = [
            'outbound_products_not_found' => [
                ['unknown_id' => 99, 'unknown_quantity' => 99],
            ]
        ];

        $validator = Validator::make($data, $rule);

        Request::capture()->replace($data);

        $this->assertTrue($validator->fails());

        $this->assertEquals('Create outbound order require at least one product', $validator->errors()->first());
    }

    public function test_ValidateProductExist_whenRequestedProductExist_shouldPass()
    {
        $products = factory(Product::class, 1)->create(['user_id' => $this->user->id])->each(function ($p) {
            $lot = factory(Lot::class)->create(['user_id' => $this->user->id]);
            $lot->products()->attach($p->id, ['quantity' => 1]);
        });

        $rule = ['outbound_products.*' => 'product_exist'];

        $data = [
            'outbound_products' => [
                ['id' => 1, 'quantity' => 1],
            ]
        ];

        $validator = Validator::make($data, $rule);

        Request::capture()->replace($data);

        $this->assertTrue($validator->passes());
    }

    public function test_ValidateProductExist_whenRequestedProductDoesntExist_shouldFail()
    {
        $products = factory(Product::class, 2)->create(['user_id' => $this->user->id])->each(function ($p) {
            $lot = factory(Lot::class)->create(['user_id' => $this->user->id]);
            $lot->products()->attach($p->id, ['quantity' => 1]);
        });

        $rule = ['outbound_products.*' => 'product_exist'];

        $data = [
            'outbound_products' => [
                ['id' => 10, 'quantity' => 1],
            ]
        ];

        $validator = Validator::make($data, $rule);

        Request::capture()->replace($data);

        $this->assertTrue($validator->fails());

        $this->assertEquals('One of the selected outbound product doesn\'t exist in your lot', $validator->errors()->first());
    }

    public function test_ValidateProductStock_whenRequestedProductWithinStockLimit_shouldPass()
    {
        $products = factory(Product::class, 1)->create(['user_id' => $this->user->id])->each(function ($p) {
            $lot = factory(Lot::class)->create(['user_id' => $this->user->id]);
            $lot->products()->attach($p->id, ['quantity' => 10]);
        });

        $rule = [
            'outbound_products.*' => 'product_stock',
        ];

        $data = [
            'outbound_products' => [
                ['id' => $products->get(0)->id, 'quantity' => 10],
            ]
        ];

        $validator = Validator::make($data, $rule);

        Request::capture()->replace($data);

        $this->assertTrue($validator->passes());
    }

    public function test_ValidateProductStock_whenRequestedProductExceedStockLimit_shouldFail()
    {
        $products = factory(Product::class, 2)->create(['user_id' => $this->user->id])->each(function ($p) {
            $lot = factory(Lot::class)->create(['user_id' => $this->user->id]);
            $lot->products()->attach($p->id, ['quantity' => 20]);
        });

        $rule = [
            'outbound_products.*' => 'product_stock',
        ];

        $data = [
            'outbound_products' => [
                ['id' => $products->get(0)->id, 'quantity' => 21],
                ['id' => $products->get(1)->id, 'quantity' => 20],
            ]
        ];

        $validator = Validator::make($data, $rule);

        Request::capture()->replace($data);

        $this->assertTrue($validator->fails());

        $this->assertEquals("The quantity of ". $products->get(0)->name ." you requested for outbound has exceeded in your lot",
            $validator->errors()->first());
    }

    public function test_ValidateProductExistAndStock_whenProductNotExist()
    {
        $products = factory(Product::class, 1)->create(['user_id' => $this->user->id])->each(function ($p) {
            $lot = factory(Lot::class)->create(['user_id' => $this->user->id]);
            $lot->products()->attach($p->id, ['quantity' => 20]);
        });

        $rule = [
            'outbound_products.*' => 'bail|product_exist|product_stock',
        ];

        $data = [
            'outbound_products' => [
                ['id' => 20, 'quantity' => 21],
            ]
        ];

        $validator = Validator::make($data, $rule);

        Request::capture()->replace($data);

        $this->assertTrue($validator->fails());

        $this->assertEquals(1, $validator->errors()->count());

        $this->assertEquals("One of the selected outbound product doesn't exist in your lot", $validator->errors()->first());
    }

    public function test_ValidateProductExistAndStock_whenProductExistButExceedStockLimit()
    {
        $products = factory(Product::class, 1)->create(['user_id' => $this->user->id])->each(function ($p) {
            $lot = factory(Lot::class)->create(['user_id' => $this->user->id]);
            $lot->products()->attach($p->id, ['quantity' => 20]);
        });

        $rule = [
            'outbound_products.*' => 'bail|product_exist|product_stock',
        ];

        $data = [
            'outbound_products' => [
                ['id' => $products->get(0)->id, 'quantity' => 21],
            ]
        ];

        $validator = Validator::make($data, $rule);

        Request::capture()->replace($data);

        $this->assertTrue($validator->fails());

        $this->assertEquals(1, $validator->errors()->count());

        $this->assertEquals("The quantity of ". $products->get(0)->name ." you requested for outbound has exceeded in your lot",
            $validator->errors()->first());
    }

    public function test_ValidateProductExistAndStock_whenProductExistAndWithinStockLimit()
    {
        $products = factory(Product::class, 3)->create(['user_id' => $this->user->id])->each(function ($p) {
            $lot = factory(Lot::class)->create(['user_id' => $this->user->id]);
            $lot->products()->attach($p->id, ['quantity' => 10]);
        });

        $rule = [
            'outbound_products.*' => 'bail|product_exist|product_stock',
        ];

        $data = [
            'outbound_products' => [
                ['id' => $products->get(0)->id, 'quantity' => 10],
                ['id' => $products->get(1)->id, 'quantity' => 9],
                ['id' => $products->get(2)->id, 'quantity' => 1],
            ]
        ];

        $validator = Validator::make($data, $rule);

        Request::capture()->replace($data);

        $this->assertTrue($validator->passes());

        $this->assertEquals(0, $validator->errors()->count());
    }
}
