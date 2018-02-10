<?php

namespace Tests\Unit;

use App\Payment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
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

        //Mock authenticated user
        $this->user = factory(\App\User::class)->create(['name' => 'test']);
        $this->actingAs($this->user);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUploadImageFile()
    {
        Storage::fake('testing');

        $response = $this->call('POST', 'payment/store',
            ['bankPaymentSlip' => UploadedFile::fake()->image('bank-transfer-slip.jpg')],
            [],
            [],
            ['HTTP_REFERER' => 'payment/index']);

        $this->assertTrue($response->isRedirect());
        $response->assertRedirect('payment/index');
        $this->assertNotNull($payment = Payment::get()->first());
        Storage::disk('testing')->assertExists($payment->picture);
    }

    public function testApprovePayments()
    {
        $payments = factory(\App\Payment::class, 2)->create();

        $response = $this->call('POST', 'payment/approve', [
                'payments' => ['1', '2']
            ],
            [],
            [],
            ['HTTP_REFERER' => 'payment/index']);

        $this->assertTrue($response->isRedirect());
        $response->assertRedirect('payment/index');

        $expect = Payment::all()->filter(function($p) {
            return $p->status === 'true';
        });

        $this->assertEquals(2, $expect->count());
    }
}
