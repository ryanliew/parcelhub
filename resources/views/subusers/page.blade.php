@extends('layouts.app')

@section('content')
	<div class="container">
		<section class="section">
			<div class="columns">
				<div class="column is-one-fifth">
					@include('components.side-menu')
				</div>
				<div class="column">
					<subusers-page can_manage="{{ auth()->user()->hasRole('admin') || auth()->user()->hasRole('user') || auth()->user()->hasRole('superadmin') }}" parent="{{ auth()->user()->id }}"></subusers-page>
				</div>	
			</div>
		</section>
	</div>
@endsection