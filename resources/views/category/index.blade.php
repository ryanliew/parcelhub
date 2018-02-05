@extends('layout.admin.dashboard')
@section('body')
<div class="container">
  <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#lotCreation">Create new category for lot</button>
  <h2>Categories</h2>
  <table>
    <tr>
      <th>
        <label>ID</label>
      </th>
      <th>
        <label>Name</label>
      </th>
      <th>
        <label>Volume</label>
      </th>
      <th>
        <label>Edit</label>
      </th>
      <th>
        <label>Delete</label>
      </th>
    </tr>
    @forelse($categories as $category)
      <tr>
        <td>{{$category->id}}</td>
        <td>{{$category->name}}</td>
        <td>{{$category->volume}}</td>
        <td><button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#lotModification-{{$category->id}}">Edit</button></td>
        <td><a href="delete/{{$category->id}}" /><button type="button" class="btn btn-info btn-lg">Delete</button></td>
      <tr>
    @empty
      <tr>
        <td>No result found.</td>
      <tr>
    @endforelse
  </table>

<!-- MODAL EDIT START -->
@foreach($categories as $category)
  <div class="modal fade" id="lotModification-{{$category->id}}" role="dialog">
    <div class="modal-dialog">
    
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Category Edit</h4>
        </div>
        <div class="modal-body">
          <form action="update" method="post">
            <input type="hidden" name="_token" value="{{csrf_token()}}"/>
            <div>
              <label>Name: </label>
              <input type="text" name="name" value="{{$category->name}}"/>
            </div>
            <div>
              <label>Volume(cm3): </label>
              <input type="number" name="volume" value="{{$category->volume}}"/>
            </div>
            <input type="hidden" name="id" value="{{$category->id}}" />
            <div class="modal-footer">
              <input type="submit" value="Submit">
            </div>
          </form>
        </div>
      </div> 
    </div>
  </div>
@endforeach
<!-- MODAL EDIT END -->

<!-- MODAL CREATION START -->
  <div class="modal fade" id="lotCreation" role="dialog">
    <div class="modal-dialog">
    
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Category Creation</h4>
        </div>
        <div class="modal-body">
          <form action="store" method="post">
            <input type="hidden" name="_token" value="{{csrf_token()}}"/>
          	<div>
          		<label>Name: </label>
          		<input type="text" name="name" />
          	</div>
          	<div>
          		<label>Volume(cm3): </label>
          		<input type="number" name="volume" />
          	</div>
          	<div class="modal-footer">
          		<input type="submit" value="Submit">
          	</div>
          </form>
        </div>
      </div> 
    </div>
  </div>
<!-- MODAL CREATION END -->
</div>
@endsection