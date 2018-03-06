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
                    <form method="POST" action="{{ route('register') }}">
                        {{ csrf_field() }}
                        <div class="field">
                            <text-input 
                                label="Name" 
                                required="true"
                                name="name"
                                error="{{ $errors->first('name') }}"
                                defaultValue="{{ old('name') }}"
                                editable="true"
                                :focus="true"
                                >
                            </text-input>
                        </div>

                        <div class="field">
                            <text-input 
                                label="E-Mail Address" 
                                required="true"
                                name="email"
                                error="{{ $errors->first('email') }}"
                                defaultValue="{{ old('email') }}"
                                editable="true"
                                >
                            </text-input>
                        </div>
                        

                        <div class="field">
                            <text-input 
                                label="Password" 
                                required="true"
                                name="password"
                                type="password"
                                error="{{ $errors->first('password') }}"
                                defaultValue="{{ old('password') }}"
                                editable="true"
                                >
                            </text-input>
                        </div>

                        <div class="field">
                            <text-input 
                                label="Confirm password" 
                                required="true"
                                name="password_confirmation"
                                type="password"
                                error="{{ $errors->first('password_confirmation') }}"
                                defaultValue="{{ old('password_confirmation') }}"
                                editable="true"
                                >
                            </text-input>
                        </div>

                        <div class="field">
                            <div class="has-text-centered">
                                <button type="submit" class="button is-primary">Register</button>
                            </div>
                        </div>
                        <div class="field">
                            <div class="has-text-centered">
                                <a href="{{ route('auth.social.redirect', ['provider' => 'facebook']) }}" class="button is-facebook">
                                    <i class="fa fa-facebook"></i>
                                    <span class="pl-5">Facebook Register</span>
                                </a>
                                <a href="{{ route('auth.social.redirect', ['provider' => 'google']) }}" class="button is-google-plus">
                                    <i class="fa fa-google"></i> 
                                    <span class="pl-5">Google+ Register</span>
                                </a>
                            </div>
                        </div>
                        <div class="has-text-centered">
                            <a href="/login" class="mt-10">Already have an account?</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
