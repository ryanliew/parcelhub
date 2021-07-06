@extends('layouts.app')

@section('content')
	<div class="container">
		<section class="section">
			<div class="columns">
				<div class="column is-one-fifth">
					@include('components.side-menu')
				</div>
				<div class="column">
					<couriers-page></couriers-page>
				</div>	
			</div>
		</section>
	</div>
@endsection