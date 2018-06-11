@extends('layouts.app')

@section('content')
<section class="hero is-light is-fullheight">
    <div class="hero-body">
        <div class="container">
            <div class="column is-4 is-offset-4">
                <div class="box">
                    <div class="has-text-centered">
                        <h2 class="is-size-4">
                            ParcelHub
                        </h2>
                    </div>
                    @if($errors->any())
                        <article class="message is-danger">
                            <div class="message-body">
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </article>
                    @endif
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
                                
                        <div class="field mt-30">
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
                                {{-- <a href="{{ route('auth.social.redirect', ['provider' => 'facebook']) }}" class="button is-facebook">
                                    <i class="fa fa-facebook"></i>
                                    <span class="pl-5">Facebook Login</span>
                                </a>
                                <a href="{{ route('auth.social.redirect', ['provider' => 'google']) }}" class="button is-google-plus">
                                    <i class="fa fa-google"></i> 
                                    <span class="pl-5">Google+ Login</span>
                                </a> --}}
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
