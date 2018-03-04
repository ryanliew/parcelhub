<?php

namespace Tests\Unit;

use App\Courier;
use App\Lot;
use App\Outbound;
use App\OutboundProduct;
use App\Product;
use App\User;
use Faker\Factory as Faker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OutboundTest extends TestCase
{
    private $user;

    public function setUp()
    {
        parent::setUp();

        $this->artisan('migrate');

        //Mock authenticated user
        $this->user = factory(User::class)->create(['name' => 'test_user']);

        factory(Courier::class)->create();

        factory(Product::class, 10)->create([
            'user_id' => $this->user->id,
            'height' => 10,
            'width' => 10,
            'length' => 10
        ]);

        $this->actingAs($this->user);
    }

    public function testOutbound_amountInsuredNotPresentWhenInsuranceIsTrue_shouldFail()
    {
        $faker = Faker::create();

        $response = $this->ajaxPost('outbound/store',
            [
                'courier_id' => 1,
                'recipient_name' => $faker->name,
                'recipient_address' => $faker->address,
                'insurance' => true,
                'outbound_products' => json_encode(['id' => 1, 'quantity' => 8]),
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'amount_insured' => ['The amount insured field is required when insurance is 1.']
            ]);
    }

    public function testOutbound_negativeAmountInsured_shouldFail()
    {
        $faker = Faker::create();

        $outbound_products = [
            ['id' => 1, 'quantity' => 8],
        ];

        $response = $this->ajaxPost('outbound/store',
            [
                'courier_id' => 1,
                'recipient_name' => $faker->name,
                'recipient_address' => $faker->address,
                'insurance' => true,
                'amount_insured' => -123456.35,
                'outbound_products' => json_encode($outbound_products),
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'amount_insured' => ['The amount insured must be at least 0.']
            ]);
    }

    public function testOutbound_whenRequestProductDoesntExistInLot_shouldFail()
    {
        $faker = Faker::create();

        $lot = factory(Lot::class)->create(['user_id' => $this->user->id, 'left_volume' => 0]);

        $lot->products()->attach(1, ['quantity' => 10]);

        $outbound_products = [['id' => 20, 'quantity' => 20]];

        $response = $this->ajaxPost('outbound/store',
            [
                'courier_id' => 1,
                'recipient_name' => $faker->name,
                'recipient_address' => $faker->address,
                'insurance' => true,
                'amount_insured' => 1234,
                'outbound_products' => json_encode($outbound_products),
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'outbound_products.0' => [
                    "One of the selected outbound product doesn't exist in your lot"
                ]
            ]);
    }

    public function testOutbound_whenRequestProductExceedStockLimit_shouldFail()
    {
        $faker = Faker::create();

        $lot = factory(Lot::class)->create(['user_id' => $this->user->id, 'left_volume' => 0]);

        $lot->products()->attach(1, ['quantity' => 10]);

        $outbound_products = [
            ['id' => 1, 'quantity' => 20],
        ];

        $response = $this->ajaxPost('outbound/store',
            [
                'courier_id' => 1,
                'recipient_name' => $faker->name,
                'recipient_address' => $faker->address,
                'insurance' => true,
                'amount_insured' => 1234,
                'outbound_products' => json_encode($outbound_products),
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'outbound_products.0' => [
                    'The quantity of '. Product::find(1)->name .' you requested for outbound has exceeded in your lot'
                ]
            ]);
    }

    public function testOutbound_getOneProductFromSingleLot()
    {
        $faker = Faker::create();

        $lot = factory(Lot::class)->create(['user_id' => $this->user->id, 'left_volume' => 0]);

        $lot->products()->attach(1, ['quantity' => 1]);

        $outbound_products = [
            ['id' => 1, 'quantity' => 1]
        ];

        $response = $this->ajaxPost('outbound/store',
            [
                'courier_id' => 1,
                'recipient_name' => $faker->name,
                'recipient_address' => $faker->address,
                'insurance' => true,
                'amount_insured' => 1999.50,
                'outbound_products' => json_encode($outbound_products),
            ]
        );

        $response
            ->assertStatus(200)
            ->assertJson([
               'message' => 'Outbound order created successfully'
            ]);

        $this->assertEquals(1000, Lot::find(1)->left_volume);

        $this->assertEquals(0, Lot::find(1)->products->count());

        $this->assertEquals(1, Outbound::all()->count());

        $outbound = Outbound::firstOrFail();

        $this->assertEquals(1, $outbound->products->count());

        $this->assertEquals(1, $outbound->insurance);

        $this->assertEquals(1999.50, $outbound->amount_insured);

        $outbound_product = $outbound->products->get(0);

        $this->assertEquals(1, $outbound_product->pivot->quantity);

        $this->assertEquals(1, $outbound_product->pivot->lot_id);
    }

    public function testOutbound_getPartialProductFromMultipleLot()
    {
        $faker = Faker::create();

        factory(Lot::class, 2)->create([
            'user_id' => $this->user->id,
            'left_volume' => 0,
        ]);

        Lot::find(1)->products()->attach(1, ['quantity' => 5]);
        Lot::find(2)->products()->attach(1, ['quantity' => 10]);

        $outbound_products = [
            ['id' => 1, 'quantity' => 10]
        ];

        $response = $this->ajaxPost('outbound/store',
            [
                'courier_id' => 1,
                'recipient_name' => $faker->name,
                'recipient_address' => $faker->address,
                'insurance' => false,
                'amount_insured' => 0,
                'outbound_products' => json_encode($outbound_products),
            ]
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Outbound order created successfully'
            ]);

        $this->assertEquals(5000, Lot::find(1)->left_volume);

        $this->assertEquals(0, Lot::find(1)->products->count());

        $this->assertEquals(5000, Lot::find(2)->left_volume);

        $this->assertEquals(1, Lot::find(2)->products->count());

        $lot_product = Lot::find(2)->products->get(0);

        $this->assertEquals(5, $lot_product->pivot->quantity);

        $this->assertEquals(1, Outbound::all()->count());

        $outbound = Outbound::firstOrFail();

        $this->assertEquals(2, $outbound->products->count());

        $outbound_product0 = $outbound->products->get(0);

        $this->assertEquals(5, $outbound_product0->pivot->quantity);

        $this->assertEquals(1, $outbound_product0->pivot->lot_id);

        $outbound_product1 = $outbound->products->get(1);

        $this->assertEquals(5, $outbound_product1->pivot->quantity);

        $this->assertEquals(2, $outbound_product1->pivot->lot_id);
    }

    public function testOutbound_getDifferentPartialProductFromSameLot()
    {
        $faker = Faker::create();

        factory(Lot::class, 2)->create([
            'user_id' => $this->user->id,
            'left_volume' => 0,
        ]);

        Lot::find(1)->products()->attach(1, ['quantity' => 20]);
        Lot::find(1)->products()->attach(2, ['quantity' => 10]);
        Lot::find(2)->products()->attach(1, ['quantity' => 10]);
        Lot::find(2)->products()->attach(2, ['quantity' => 10]);

        $outbound_products = [
            ['id' => 1, 'quantity' => 18],
            ['id' => 2, 'quantity' => 15],
        ];

        $response = $this->ajaxPost('outbound/store',
            [
                'courier_id' => 1,
                'recipient_name' => $faker->name,
                'recipient_address' => $faker->address,
                'insurance' => false,
                'amount_insured' => 0,
                'outbound_products' => json_encode($outbound_products),
            ]
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Outbound order created successfully'
            ]);

        $this->assertEquals(28000, Lot::find(1)->left_volume);
        $this->assertEquals(5000, Lot::find(2)->left_volume);

        $product1 = Product::with('lots')
            ->where('id', 1)
            ->where('user_id', $this->user->id)
            ->first();

        $this->assertEquals(12, $product1->total_quantity);

        $product2 = Product::with('lots')
            ->where('id', 2)
            ->where('user_id', $this->user->id)
            ->first();

        $this->assertEquals(5, $product2->total_quantity);

        $this->assertEquals(1, Outbound::all()->count());

        $outbound = Outbound::firstOrFail();

        $this->assertEquals(3, $outbound->products->count());

        $outbound_product_0 = $outbound->products->get(0);

        $this->assertEquals(18, $outbound_product_0->pivot->quantity);
        $this->assertEquals(1, $outbound_product_0->pivot->lot_id);

        $outbound_product_1 = $outbound->products->get(1);

        $this->assertEquals(10, $outbound_product_1->pivot->quantity);
        $this->assertEquals(1, $outbound_product_1->pivot->lot_id);

        $outbound_product_2 = $outbound->products->get(2);

        $this->assertEquals(5, $outbound_product_2->pivot->quantity);
        $this->assertEquals(2, $outbound_product_2->pivot->lot_id);
    }

    public function testOutbound_getMultiplePartialProductFromMultipleLot()
    {
        $faker = Faker::create();

        factory(Lot::class, 5)->create([
            'user_id' => $this->user->id,
            'left_volume' => 0,
        ]);

        Lot::find(1)->products()->attach(1, ['quantity' => 5]);
        Lot::find(2)->products()->attach(1, ['quantity' => 5]);
        Lot::find(3)->products()->attach(2, ['quantity' => 5]);
        Lot::find(4)->products()->attach(3, ['quantity' => 5]);
        Lot::find(5)->products()->attach(2, ['quantity' => 5]);

        $outbound_products = [
            ['id' => 1, 'quantity' => 8],
            ['id' => 2, 'quantity' => 6],
            ['id' => 3, 'quantity' => 1],
        ];

        $response = $this->ajaxPost('outbound/store',
            [
                'courier_id' => 1,
                'recipient_name' => $faker->name,
                'recipient_address' => $faker->address,
                'insurance' => false,
                'amount_insured' => 0,
                'outbound_products' => json_encode($outbound_products),
            ]
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Outbound order created successfully'
            ]);

        $this->assertEquals(5000, Lot::find(1)->left_volume);
        $this->assertEquals(0, Lot::find(1)->products->count());

        $this->assertEquals(3000, Lot::find(2)->left_volume);
        $this->assertEquals(1, Lot::find(2)->products->count());
        $lot2_product = Lot::find(2)->products->get(0);
        $this->assertEquals(2, $lot2_product->pivot->quantity);

        $this->assertEquals(5000, Lot::find(3)->left_volume);
        $this->assertEquals(0, Lot::find(3)->products->count());

        $this->assertEquals(1000, Lot::find(4)->left_volume);
        $this->assertEquals(1, Lot::find(4)->products->count());
        $lot4_product = Lot::find(4)->products->get(0);
        $this->assertEquals(4, $lot4_product->pivot->quantity);

        $this->assertEquals(1000, Lot::find(5)->left_volume);
        $this->assertEquals(1, Lot::find(5)->products->count());
        $lot5_product = Lot::find(5)->products->get(0);
        $this->assertEquals(4, $lot5_product->pivot->quantity);

        $this->assertEquals(1, Outbound::all()->count());

        $outbound = Outbound::firstOrFail();

        $this->assertEquals(5, $outbound->products->count());

        $outbound_product0 = $outbound->products->get(0);
        $this->assertEquals(5, $outbound_product0->pivot->quantity);
        $this->assertEquals(1, $outbound_product0->pivot->product_id);
        $this->assertEquals(1, $outbound_product0->pivot->lot_id);

        $outbound_product1 = $outbound->products->get(1);
        $this->assertEquals(3, $outbound_product1->pivot->quantity);
        $this->assertEquals(1, $outbound_product1->pivot->product_id);
        $this->assertEquals(2, $outbound_product1->pivot->lot_id);

        $outbound_product2 = $outbound->products->get(2);
        $this->assertEquals(5, $outbound_product2->pivot->quantity);
        $this->assertEquals(2, $outbound_product2->pivot->product_id);
        $this->assertEquals(3, $outbound_product2->pivot->lot_id);

        $outbound_product3 = $outbound->products->get(3);
        $this->assertEquals(1, $outbound_product3->pivot->quantity);
        $this->assertEquals(2, $outbound_product3->pivot->product_id);
        $this->assertEquals(5, $outbound_product3->pivot->lot_id);

        $outbound_product4 = $outbound->products->get(4);
        $this->assertEquals(1, $outbound_product4->pivot->quantity);
        $this->assertEquals(3, $outbound_product4->pivot->product_id);
        $this->assertEquals(4, $outbound_product4->pivot->lot_id);
    }
}
