<?php

namespace App\Http\Controllers;

use App\PaymentGatewayDefinition;
use Illuminate\Http\Request;

class PaymentGatewayDefinitionController extends Controller
{
    public function index()
    {
        return PaymentGatewayDefinition::active()->get();
    }
}
