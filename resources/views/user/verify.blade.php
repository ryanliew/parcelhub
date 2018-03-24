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

                    @if(isset($message))
                        <article class="message is-info">
                            <div class="message-body has-text-centered">
                                {{ $message }}
                            </div>
                        </article>

                        @if(isset($id))
                            <form method="POST" action="{{ route('resend', ['id' => $id] ) }}">
                                {{ csrf_field() }}
                                <div class="has-text-centered">
                                    <button class="button is-primary" type="submit">Resend Verification Email</button>
                                </div>
                            </form>
                        @else
                            <div class="has-text-centered">
                                <a class="button is-primary" role="button" href="{{ route('home') }}">Proceed to login</a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
