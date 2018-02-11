@extends('layout.admin.dashboard')
@section('body')

    <h1>Admin Lots Management</h1>

    <table class="table">
        <tr>
            <th>Username</th>
            <th>Category</th>
            <th>Lot Name</th>
            <th>Volume</th>
            <th>Status</th>
        </tr>

        @foreach($lots as $lot)
            <tr>
                <td>{{ $lot->user->name }}</td>
                <td>{{ $lot->category->name }}</td>
                <td>{{ $lot->name }}</td>
                <td>{{ $lot->volume }}</td>
                <td>{{ $lot->status }}</td>
            </tr>
        @endforeach

    </table>
@endsection
