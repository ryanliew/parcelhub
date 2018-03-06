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
                    <form method="POST" action="{{ route('password.request') }}">
                        {{ csrf_field() }}

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="field">
                            <text-input 
                                label="E-Mail Address" 
                                required="true"
                                name="email"
                                error="{{ $errors->first('email') }}"
                                defaultValue="{{ $email or old('email') }}"
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
                                editable="true"
                                >
                            </text-input>
                        </div>

                         <div class="field">
                            <div class="has-text-centered">
                                <button type="submit" class="button is-danger">Reset password</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
