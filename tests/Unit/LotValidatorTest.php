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

class LotValidatorTest extends TestCase
{
    private $user;

    public function setUp()
    {
        parent::setUp();

        $this->artisan('migrate');

        //Mock authenticated user
        $this->user = factory(User::class)->create(['name' => 'test_user']);

        factory(Lot::class)->create(['user_id' => $this->user->id]);

        factory(Product::class)->create();

        $this->actingAs($this->user);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testLotScapeValidation_shouldPass_WithAvailableLotVolume()
    {

        $rule = ['product' => 'lot_space'];

        $data = [
            'product' => [
                ['id' => 1, 'quantity' => 1]
            ]
        ];

        $validator = Validator::make($data, $rule);

        Request::capture()->replace($data);

        $this->assertFalse($validator->passes());
    }

    public function testLotScapeValidation_shouldFail_IfTotalProductVolumeLargerThanAvailableLotVolume()
    {
        factory(Lot::class)->create(['user_id' => $this->user->id]);
        factory(Product::class)->create();

        $rule = ['product' => 'lot_space'];

        $data = [
            'product' => [
                ['id' => 1, 'quantity' => 100]
            ]
        ];

        $validator = Validator::make($data, $rule);

        Request::capture()->replace($data);

        $this->assertFalse($validator->fails());
    }

    public function testLotScapeValidation_shouldFail_IfProductIDIsNotPresent()
    {
        $rule = ['product' => 'lot_space'];

        $data = [
            'product' => [
                ['idX' => 1, 'quantity' => 100]
            ]
        ];

        $validator = Validator::make($data, $rule);

        Request::capture()->replace($data);

        $this->assertFalse($validator->fails());
    }

    public function testLotScapeValidation_shouldFail_IfProductQuantityIsNotPresent()
    {
        $rule = ['product' => 'lot_space'];

        $data = [
            'product' => [
                ['idX' => 1, 'quantityX' => 100]
            ]
        ];

        $validator = Validator::make($data, $rule);

        Request::capture()->replace($data);

        $this->assertFalse($validator->fails());
    }

    public function testLotScapeValidation_shouldFail_IfBothProductIDAndQuantityAreNotPresent()
    {
        $rule = ['product' => 'lot_space'];

        $data = [
            'product' => [
                ['this_id_not_id' => 1, 'this_is_not_quantity' => 100]
            ]
        ];

        $validator = Validator::make($data, $rule);

        Request::capture()->replace($data);

        $this->assertFalse($validator->fails());
    }
}
