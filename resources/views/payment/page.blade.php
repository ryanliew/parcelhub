@extends('layouts.app')

@section('content')
	<div class="container">
		<section class="section">
			<div class="columns">
				<div class="column is-one-fifth">
					@include('components.side-menu')
				</div>
				<div class="column">
					<payments-page can_manage="{{ auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin') }}"></payments-page>
				</div>	
			</div>
		</section>
	</div>
@endsection

@section('js')
	<script type="text/javascript" src="/js/vendors.js"></script>
@endsection