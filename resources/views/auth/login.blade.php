@extends('layouts.app')

@section('content')
<section class="hero is-primary is-fullheight">
    <div class="hero-body">
        <div class="container">
            <div class="column is-4 is-offset-4">
                <div class="box">
                    <div class="has-text-centered">
                        <h2 class="is-size-4">
                            ParcelHub
                        </h2>
                    </div>
                    <form method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}
                        <div class="field">
                            <text-input 
                                label="Email" 
                                required="true"
                                name="email"
                                editable="true"
                                error="{{ $errors->first('email') }}"
                                :focus="true"
                                >
                            </text-input>
                        </div>
                        
                        <div class="field">
                            <text-input 
                                label="Password" 
                                required="true"
                                name="password"
                                type="password"
                                editable="true"
                                error="{{ $errors->first('password') }}"
                                :focus=false
                                >
                            </text-input>
                        </div>
                                
                        <div class="field">
                            <checkbox-input
                                label="Keep me logged in"
                                name="remember"
                                editable="true">
                            </checkbox-input>
                        </div>
                        
                        <div class="field">
                            <div class="has-text-centered">
                                <button type="submit" class="button is-primary">Sign in</button>
                            </div>
                        </div>
                        <div class="field">
                            <div class="has-text-centered">
                                <a href="{{ route('auth.social.redirect', ['provider' => 'facebook']) }}" class="button is-facebook">
                                    <i class="fab fa-facebook"></i>
                                    <span class="pl-5">Facebook Login</span>
                                </a>
                                <a href="{{ route('auth.social.redirect', ['provider' => 'google']) }}" class="button is-google-plus">
                                    <i class="fab fa-google"></i> 
                                    <span class="pl-5">Google+ Login</span>
                                </a>
                            </div>
                        </div>
                        <div class="has-text-centered">
                            <a href="{{ route('register') }}" class="mt-10">Don't have an account?</a>
                        </div>

                        <div class="has-text-centered">
                            <a href="{{ route('password.request') }}" class="mt-10">Forgot Your Password?</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
