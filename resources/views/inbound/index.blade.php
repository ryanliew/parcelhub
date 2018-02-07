@extends('layout.admin.dashboard')
@section('body')
<div class="container">
  <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#lotCreation">Create new inbound</button>
  <h2>Inbounds</h2>
  <table>
    <tr>
      <th>
        <label>ID</label>
      </th>
      <th>
        <label>Product</label>
      </th>
      <th>
        <label>Quantity</label>
      </th>
      <th>
        <label>Arrival Date</label>
      </th>
      <th>
        <label>Total carton</label>
      </th>
    </tr>
    @forelse($inbounds as $inbound)
      <tr>
        <td>{{$inbound->id}}</td>
        <td>{{$inbound->product}}</td>
        <td>{{$inbound->quantity}}</td>
        <td>{{$inbound->arrival_date}}</td>
        <td>{{$inbound->total_carton}}</td>
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
});
</script>

@endsection