<?php

namespace Tests\Unit;

use App\Lot;
use App\Payment;
use App\Settings;
use App\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaymentTest extends TestCase
{
    private $user;

    public function setUp()
    {
        parent::setUp();

        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => 'SettingTableSeeder']);

        //Mock authenticated user
        $user = factory(User::class)->create(['name' => 'test']);

        $this->user = User::find($user->id);

        $this->actingAs($this->user);
    }

    public function test_purchase_withExistingLotAndUploadedPaymentSlip_shouldPass()
    {
        $lot = factory(Lot::class)->create();

        Storage::fake('testing');

        $lot_purchases = [
            ['id' => $lot->id, 'rental_duration' => $lot->rental_duration]
        ];

        $response = $this->ajaxPost('payment/purchase',
            [
                'lot_purchases' => json_encode($lot_purchases),
                'payment_slip' => UploadedFile::fake()->image('bank-transfer-slip.jpg')
            ]
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully purchase'
            ]);

        $this->assertNotNull($payment = Payment::get()->first());

        Storage::disk('testing')->assertExists($payment->picture);

        $this->assertEquals(1, $this->user->payments->count());

        $this->assertEquals(1, $this->user->lots->count());

        $user_lot = $this->user->lots->first();

        $this->assertEquals($lot->rental_duration, $user_lot->rental_duration);
    }

    public function test_purchase_withRentalDurationLowerThanDefaultSetting_shouldFail()
    {
        $lot = factory(Lot::class)->create();

        Storage::fake('testing');

        $lot_purchases = [
            ['id' => $lot->id, 'rental_duration' => 10]
        ];

        $response = $this->ajaxPost('payment/purchase',
            [
                'lot_purchases' => json_encode($lot_purchases),
                'payment_slip' => UploadedFile::fake()->image('bank-transfer-slip.jpg'),
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                "lot_purchases.0.rental_duration" => [
                    "Minimum rental duration must not least than ". Settings::rentalDuration() ." days"
                ]
            ]);
    }

    public function test_purchase_userOverrideDefaultRentalDuration_shouldPass()
    {
        $lot = factory(Lot::class)->create();

        Storage::fake('testing');

        $lot_purchases = [
            ['id' => $lot->id, 'rental_duration' => 90]
        ];

        $response = $this->ajaxPost('payment/purchase',
            [
                'lot_purchases' => json_encode($lot_purchases),

                'payment_slip' => UploadedFile::fake()->image('bank-transfer-slip.jpg'),
            ]
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully purchase'
            ]);

        $user_lot = $this->user->lots->first();

        $this->assertEquals(90, $user_lot->rental_duration);
    }

    public function test_purchase_userAbleToPurchaseMultipleExistingLots_shouldPass()
    {
        $lots = factory(Lot::class, 4)->create(['status' => 'false']);

        $lot_purchases = [];

        foreach ($lots as $lot) {
            $lot_purchases[] = ['id' => $lot->id, 'rental_duration' => 90];
        }

        Storage::fake('testing');

        $response = $this->ajaxPost('payment/purchase',
            [
                'lot_purchases' => json_encode($lot_purchases),

                'payment_slip' => UploadedFile::fake()->image('bank-transfer-slip.jpg'),
            ]
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully purchase'
            ]);

        $this->assertEquals(1, $this->user->payments->count());

        $this->assertEquals(4, $this->user->lots->count());

        foreach ($this->user->lots as $lot) {
            $this->assertEquals('false', $lot->status);
            $this->assertEquals(90, $lot->rental_duration);
        }
    }
//
//    public function testApprovePayments()
//    {
//        $payments = factory(\App\Payment::class, 2)->create();
//
//        $response = $this->call('POST', 'payment/approve', [
//                'payments' => ['1', '2']
//            ],
//            [],
//            [],
//            ['HTTP_REFERER' => 'payment/index']);
//
//        $this->assertTrue($response->isRedirect());
//        $response->assertRedirect('payment/index');
//
//        $expect = Payment::all()->filter(function($p) {
//            return $p->status === 'true';
//        });
//
//        $this->assertEquals(2, $expect->count());
//    }
}
