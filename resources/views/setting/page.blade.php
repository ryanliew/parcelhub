@extends('layouts.app')

@section('content')
  <div class="container">
    <section class="section">
      <div class="columns">
        <div class="column is-one-quarter">
          @include('components.side-menu')
        </div>
        <div class="column">
            <settings-page 
                v-bind:setting="{{ json_encode($settings) }}">
            </settings-page>
        </div>  
      </div>
    </section>
  </div>
@endsection