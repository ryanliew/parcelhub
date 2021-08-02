@extends('layouts.app')

@section('content')
	<div class="container">
		<section class="section">
			<div class="columns">
				<div class="column is-one-quarter">
					@include('components.side-menu')
				</div>
				<div class="column">
					<recalls-page 
						can_manage="{{ auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin') }}" 
						fee="{{ Settings::get('cancelation_fee') }}" 
						number="{{ Settings::get('cancelation_number') }}"
						can_edit="{{ !auth()->user()->hasRole('subuser') }}">
					</recalls-page>
				</div>	
			</div>
		</section>
	</div>
@endsection