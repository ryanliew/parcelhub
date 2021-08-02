@extends('layouts.app')

@section('content')
	<div class="container">
		<section class="section">
			<div class="columns">
				<div class="column is-one-fifth">
					@include('components.side-menu')
				</div>
				<div class="column">
					<inbounds-page can_manage="{{ auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin')}}" can_edit="{{ !auth()->user()->hasRole('subuser') }}"></inbounds-page>
				</div>	
			</div>
		</section>
	</div>
@endsection