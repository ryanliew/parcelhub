<?php

namespace Tests\Unit;

use App\Courier;
use App\Lot;
use App\Outbound;
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

        $this->actingAs($this->user);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testOutbound_withOneSingleEmptyLot_and_OneProduct()
    {
        $faker = Faker::create();

        $courier = factory(Courier::class)->create();

        factory(Lot::class)->create(['user_id' => $this->user->id]);

        $product = factory(Product::class)->create(['user_id' => $this->user->id]);

        $sumOfLotSpaceLeft = $this->user->total_lot_left_volume - $product->volume;

        $response = $this->call('POST', 'outbound/store',
            [
                'courier_id' => $courier->id,
                'recipient_name' => $faker->name,
                'recipient_address' => $faker->address,
                'products' => [
                    [
                        'id' => $product->id,
                        'quantity' => '1',
                        'insurance' => 'no',
                        'dangerous' => 'no',
                    ]
                ],
            ],
            [],
            [],
            ['HTTP_REFERER' => 'outbound/index']
        );

        $this->assertTrue($response->isRedirect());

        $outbound = Outbound::all();

        $this->assertEquals(1, $outbound->count());

        $this->assertEquals($sumOfLotSpaceLeft, User::find($this->user->id)->total_lot_left_volume);

        $lot1 = Lot::find(1);
        $lot1TotalQuantity = $lot1->products()->where('product_id', $product->id)->value('quantity');
        $this->assertEquals(1, $lot1TotalQuantity);
    }

    public function testOutbound_withTwoEmptyLot_and_SingleProductWithMultipleQuantity()
    {
        $faker = Faker::create();

        $courier = factory(Courier::class)->create();

        factory(Lot::class, 2)->create([
            'user_id' => $this->user->id,
            'left_volume' => '10000'
        ]);

        $product = factory(Product::class)->create([
            'user_id' => $this->user->id,
            'height' => 10,
            'length' => 10,
            'width' => 10,
        ]);

        $sumOfLotSpaceLeft = $this->user->total_lot_left_volume - ($product->volume * 15);

        $response = $this->call('POST', 'outbound/store',
            [
                'courier_id' => $courier->id,
                'recipient_name' => $faker->name,
                'recipient_address' => $faker->address,
                'products' => [
                    [
                        'id' => $product->id,
                        'quantity' => '15',
                        'insurance' => 'no',
                        'dangerous' => 'no',
                    ]
                ],
            ],
            [],
            [],
            ['HTTP_REFERER' => 'outbound/index']
        );

        $this->assertTrue($response->isRedirect());

        $outbound = Outbound::all();

        $this->assertEquals(1, $outbound->count());

        $this->assertEquals($sumOfLotSpaceLeft, User::find($this->user->id)->total_lot_left_volume);

        $lot1 = Lot::find(1);
        $lot1TotalQuantity = $lot1->products()->where('product_id', $product->id)->value('quantity');
        $this->assertEquals(10, $lot1TotalQuantity);

        $lot2 = Lot::find(2);
        $lot2TotalQuantity = $lot2->products()->where('product_id', $product->id)->value('quantity');
        $this->assertEquals(5, $lot2TotalQuantity);
    }

    public function testOutbound_withMultipleEmptyLot_and_MultipleProductsWithQuantity()
    {
        $faker = Faker::create();

        $courier = factory(Courier::class)->create();

        factory(Lot::class, 4)->create([
            'user_id' => $this->user->id,
            'left_volume' => '10000'
        ]);

        $products = factory(Product::class, 3)->create([
            'user_id' => $this->user->id,
            'height' => 10,
            'length' => 10,
            'width' => 10,
        ]);

        $sumOfLotSpaceLeft = $this->user->total_lot_left_volume - ($products->get(0)->volume * 37);

        $response = $this->call('POST', 'outbound/store',
            [
                'courier_id' => $courier->id,
                'recipient_name' => $faker->name,
                'recipient_address' => $faker->address,
                'products' => [
                    [
                        'id' => 1,
                        'quantity' => '15',
                        'insurance' => 'no',
                        'dangerous' => 'no',
                    ],
                    [
                        'id' => 2,
                        'quantity' => '10',
                        'insurance' => 'no',
                        'dangerous' => 'no',
                    ],
                    [
                        'id' => 3,
                        'quantity' => '12',
                        'insurance' => 'no',
                        'dangerous' => 'no',
                    ]
                ],
            ],
            [],
            [],
            ['HTTP_REFERER' => 'outbound/index']
        );

        $this->assertTrue($response->isRedirect());

        $outbound = Outbound::all();

        $this->assertEquals(1, $outbound->count());

        $this->assertEquals($sumOfLotSpaceLeft, User::find($this->user->id)->total_lot_left_volume);

        $lot1 = Lot::find(1);
        $lot1TotalQuantity = $lot1->products()->where('product_id', 1)->value('quantity');
        $this->assertEquals(10, $lot1TotalQuantity);

        $lot2 = Lot::find(2);
        $lot2TotalQuantity = $lot2->products()->where('product_id', 1)->value('quantity');
        $this->assertEquals(5, $lot2TotalQuantity);

        $lot2TotalQuantity = $lot2->products()->where('product_id', 2)->value('quantity');
        $this->assertEquals(5, $lot2TotalQuantity);

        $lot3 = Lot::find(3);
        $lot3TotalQuantity = $lot3->products()->where('product_id', 2)->value('quantity');
        $this->assertEquals(5, $lot3TotalQuantity);

        $lot3TotalQuantity = $lot3->products()->where('product_id', 3)->value('quantity');
        $this->assertEquals(5, $lot3TotalQuantity);

        $lot4 = Lot::find(4);
        $lot4TotalQuantity = $lot4->products()->where('product_id', 3)->value('quantity');
        $this->assertEquals(7, $lot4TotalQuantity);
    }
}
