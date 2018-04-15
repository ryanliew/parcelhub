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
        <td>{{$category->price}}</td>
        <td><button type="button" class="openEditModal btn btn-info btn-lg" data-toggle="modal" data-target="#categoryEdit">Edit</button></td>
        <td><a href="delete/{{$category->id}}" /><button type="button" class="btn btn-info btn-lg">Delete</button></td>
      <tr>
    @empty
      <tr>
        <td>No result found.</td>
      <tr>
    @endforelse
  </table>

<!-- MODAL EDIT START -->
  <div class="modal fade" id="categoryEdit" role="dialog">
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
              <input type="text" id="name" name="name" />
            </div>
            <div>
              <label>Volume(cm3): </label>
              <input type="number" id="volume" name="volume" />
            </div>
            <div>
              <label>Price: </label>
              <input type="number" id="price" name="price" />
            </div>
            <input type="hidden" id="id" name="id" />
            <div class="modal-footer">
              <input type="submit" value="Submit">
            </div>
          </form>
        </div>
      </div> 
    </div>
  </div>
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
            <div>
              <label>Price: </label>
              <input type="number" name="price" />
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

<script>
$(document).on("click", ".openEditModal", function () {
    var $row = $(this).closest('tr');
    var $columns = $row.find('td');

    $columns.addClass('row-highlight');
    $("#id").val($columns[0].innerHTML);
    $("#name").val($columns[1].innerHTML);
    $("#volume").val($columns[2].innerHTML);
    $("#price").val($columns[3].innerHTML);
});
</script>

@endsection