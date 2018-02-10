@extends('layout.admin.dashboard')
@section('body')
<div class="container">
  <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#courierCreation">Create new courier</button>
  <h2>Couriers</h2>
  <table>
    <tr>
      <th>
        <label>ID</label>
      </th>
      <th>
        <label>Name</label>
      </th>
    </tr>
    @forelse($couriers as $courier)
      <tr>
        <td>{{$courier->id}}</td>
        <td>{{$courier->name}}</td>
        <td><button type="button" class="openEditModal btn btn-info btn-lg" data-toggle="modal" data-target="#courierEdit">Edit</button></td>
        <td><a href="delete/{{$courier->id}}" /><button type="button" class="btn btn-info btn-lg">Delete</button></td>
      <tr>
    @empty
      <tr>
        <td>No result found.</td>
      <tr>
    @endforelse
  </table>

<!-- MODAL EDIT START -->
  <div class="modal fade" id="courierEdit" role="dialog">
    <div class="modal-dialog">
    
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Courier Edit</h4>
        </div>
        <div class="modal-body">
          <form action="update" method="post">
            <input type="hidden" name="_token" value="{{csrf_token()}}"/>
            <div>
              <label>Name: </label>
              <input type="text" id="name" name="name" />
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
  <div class="modal fade" id="courierCreation" role="dialog">
    <div class="modal-dialog">
    
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Courier Creation</h4>
        </div>
        <div class="modal-body">
          <form action="store" method="post">
            <input type="hidden" name="_token" value="{{csrf_token()}}"/>
          	<div>
          		<label>Name: </label>
          		<input type="text" name="name" />
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
});
</script>

@endsection