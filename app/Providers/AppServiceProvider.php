<?php

namespace App\Providers;

use App\Inbound;
use App\Outbound;
use App\Validation\ProductValidator;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        \View::composer("*", function($view){

            $pendingInbounds = Inbound::where('process_status', 'awaiting_arrival')->get();
            $pendingOutbounds = Outbound::where('process_status', 'pending')->get();

            $hasInbound = $pendingInbounds->where('type', 'inbound')->count() > 0;
            $hasOutbound = $pendingOutbounds->where('type', 'outbound')->count() > 0;

            $hasReturn = $pendingInbounds->where('type', 'return')->count() > 0;
            $hasRecall = $pendingOutbounds->where('type', 'recall')->count() > 0;

            $view->with(['hasInbounds' => $hasInbound, 
                         'hasOutbounds' => $hasOutbound,
                         'hasReturns' => $hasReturn,
                         'hasRecalls' => $hasRecall,
                        ]);
        });
        Validator::resolver(function($translator, $data, $rules, $messages, $attribute)
        {
            return new ProductValidator($translator, $data, $rules, $messages, $attribute);
        });

        Blade::if('role', function ($expression) {
            return auth()->check() && auth()->user()->hasRole($expression);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(IdeHelperServiceProvider::class);
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
        }
    }
}
