@extends('layout.admin.dashboard')
@section('body')

    <h1>Outbound Order Report Download</h1>

    <ul class="list-group">
        @foreach($outbounds as $key => $outbound)
            <li class="list-group-item">
                {{ $key + 1 }} Outbound Report
                <a href="{{ route('download.outbound.report', ['id' => $outbound->id]) }}" class="btn btn-primary"
                   role="button">Download</a>
            </li>
        @endforeach
    </ul>
@endsection