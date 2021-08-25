<?php

namespace App\Services;

use Billplz\Client;
use Illuminate\Support\Facades\App;

class BillplzService {

    private $payment, $billplz;

    public function __construct($payment = null)
    {
        $this->billplz = Client::make(config("billplz.secret"));

        if(App::environment(['local', 'staging']))
            $this->billplz->useSandbox();

        if($payment)
            $this->payment = $payment;
    }

    public function initialize()
    {
        $collection = $this->createCollection();
        $bill = $this->createBill($collection['id']);

        $this->payment->payment_gateway_reference_id = $collection['id'];
        $this->payment->payment_gateway_billplz_billid = $bill['id'];
        $this->payment->payment_response = $bill['url'];

        $this->payment->save();
        return $this->payment;
    }

    public function createCollection()
    {
        $name =  "Collection for Payment #" . $this->payment->id;

        $collection = $this->billplz->collection();
        $response = $collection->create($name);

        return $response->toArray();
    }

    public function createBill($collectionId)
    {
        $bill = $this->billplz->bill();
        $name =  $this->payment->user->name;
        $amount = $this->payment->price * 100;
        $response = $bill->create(
            $collectionId,
            $this->payment->user->email,
            $this->payment->user->phone,
            $name,
            (string)($amount *  100),
            url(config("billplz.callback_path")),
            $name . ' bill',
            [
                'redirect_url' => url(config("billplz.redirect_path")),
                'reference_1_label' => "Bank Code",
                "reference_1" => App::environment(['local', 'staging']) ? "TEST0021" : $this->payment->gateway->code,
            ]
        );

        return $response->toArray();
    }

    public function getFPXBanks()
    {
        $bank = $this->billplz->bank();
        $fpx = $bank->supportedForFpx();

        $fpx = collect($fpx->toArray()['banks'])->filter(function($fpx){ return $fpx['active'] == true; });

        dd($fpx->toArray());
    }
}