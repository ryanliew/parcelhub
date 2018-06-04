@extends('layouts.app')

@section('content')
	<div class="container">
		<section class="section">
			<div class="columns">
				<div class="column is-one-quarter">
					@include('components.side-menu')
				</div>
				<div class="column">
					<subusers-page can_manage="{{ auth()->user()->hasRole('admin') || auth()->user()->hasRole('user') }}" parent="{{ auth()->user()->id }}"></subusers-page>
				</div>	
			</div>
		</section>
	</div>
@endsection