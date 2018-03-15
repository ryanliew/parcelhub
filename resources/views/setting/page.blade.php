@extends('layouts.app')

@section('content')
  <div class="container">
    <section class="section">
      <div class="columns">
        <div class="column is-one-quarter">
          @include('components.side-menu')
        </div>
        <div class="column">
          @if(auth()->user()->hasRole('admin'))
            <settings-page 
                v-bind:setting="{{ json_encode($settings) }}">
            </settings-page>
          @else
            <article class="message is-danger">
                <div class="message-header">
                  <p>Unauthorized</p>
                  <button class="delete" aria-label="delete"></button>
                </div>
                <div class="message-body">
                  You are unauthorized to view this page
                </div>
            </article>
          @endif
        </div>  
      </div>
    </section>
  </div>
@endsection