@extends('layouts.app')

@section('content')
	<div class="container">
		<section class="section">
			<div class="columns">
				<div class="column is-one-quarter">
					@include('components.side-menu')
				</div>
				<div class="column">
					<payments-page can_manage="{{ auth()->user()->hasRole('admin') }}"></payments-page>
				</div>	
			</div>
		</section>
	</div>
@endsection

@section('js')
	<script type="text/javascript" src="/js/vendors.js"></script>
@endsection