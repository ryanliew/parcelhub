@extends('layouts.app')

@section('content')
	<div class="container">
		<section class="section">
			<div class="columns">
				<div class="column is-one-quarter">
					@include('components.side-menu')
				</div>
				<div class="column">
					<returns-page 
						can_manage="{{ auth()->user()->hasRole('admin') }}" 
						fee="{{ Settings::get('cancelation_fee') }}" 
						number="{{ Settings::get('cancelation_number') }}">
					</returns-page>
				</div>	
			</div>
		</section>
	</div>
@endsection