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
                    <form method="POST" action="{{ route('password.email') }}">
                        {{ csrf_field() }}

                        <div class="field">
                            <text-input 
                                label="E-Mail Address" 
                                required="true"
                                name="email"
                                error="{{ $errors->first('email') }}"
                                defaultValue="{{ old('email') }}"
                                editable="true"
                                focus="true"
                                >
                            </text-input>
                        </div>

                         <div class="field">
                            <div class="has-text-centered">
                                <button type="submit" class="button is-danger">Reset password</button>
                            </div>
                        </div>
                        <div class="has-text-centered">
                            <a href="/login" class="mt-10">Back to login page</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
