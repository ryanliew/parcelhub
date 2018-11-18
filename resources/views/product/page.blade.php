@extends('layouts.app')

@section('content')
	<div class="container">
		<section class="section">
			<div class="columns">
				<div class="column is-one-fifth">
					@include('components.side-menu')
				</div>
				<div class="column">
					<products-page can_manage="{{ auth()->user()->hasRole('admin') }}" can_edit="{{ !auth()->user()->hasRole('subuser') }}"></products-page>
				</div>	
			</div>
		</section>
	</div>
@endsection