@extends('layout.admin.dashboard')
@section('body')
<div class="container">
  <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#lotCreation">Create new lot</button>
  
  <h2>Lots</h2>
  <table>
    <tr>
      <th>
        <label>ID</label>
      </th>
      <th>
        <label>Name</label>
      </th>
      <th>
        <label>Category</label>
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
    @forelse($lots as $lot)
      <tr>
        <td>{{$lot->id}}</td>
        <td>{{$lot->name}}</td>
        <td id="{{$lot->category_id}}">{{$lot->category->name}}</td>
        <td>{{$lot->volume}}</td>
        <!-- <td><button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#lotModification-{{$lot->id}}">Edit</button></td> -->
        <td><button type="button" class="openEditModal btn btn-info btn-lg" data-toggle="modal" data-target="#lotModification">Edit</button></td>
        <td><a href="delete/{{$lot->id}}" /><button type="button" class="btn btn-info btn-lg">Delete</button></td>
      <tr>
    @empty
      <tr>
        <td>No result found.</td>
      <tr>
    @endforelse
  </table>

  <!-- MODAL EDIT START -->
    <div class="modal fade" id="lotModification" role="dialog">
      <div class="modal-dialog">
      
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Lot Edit</h4>
          </div>
          <div class="modal-body">
            <form action="update" method="post">
              <input type="hidden" name="_token" value="{{csrf_token()}}"/>
              <div>
                <label>Name: </label>
                <input type="text" id="name" name="name"/>
              </div>
              <div>
                <label>Category: </label>
                <select id="category" name="category">
                  @forelse($categories as $category)
                      <option id="{{$category->volume}}" value="{{$category->id}}" >{{$category->name}}</option>
                  @empty
                    <option disabled>No category is found. Please click "Create Category" to create new category.</option>
                  @endforelse
                </select>
              </div>
              <div>
                <label>Volume(cm3): </label>
                <input type="number" id="volume" name="volume"/>
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
          <h4 class="modal-title">Lot Creation</h4>
        </div>
        <div class="modal-body">
          <form action="store" method="post">
            <input type="hidden" name="_token" value="{{csrf_token()}}"/>
          	<div>
          		<label>Name: </label>
          		<input type="text" name="name" />
          	</div>
          	<div>
          		<label>Category: </label>
          		<select id="category" name="category">
                @forelse($categories as $category)
                  <option id="{{$category->volume}}" value="{{$category->id}}">{{$category->name}}</option>
                @empty
                  <option disabled>No category is found. Please click "Create Category" to create new category.</option>
                @endforelse
          		</select>
          	</div>
            <div>
              <label>Volume(cm3): </label>
              <input type="number" id="volume-creation" name="volume" />
            </div>
            <div class="modal-footer">
            @if(count($categories) > 0)
              <a href="/category/index" /><button type="button" class="btn btn-info btn-lg">Create Category</button>
          		<input type="submit" value="Submit">
            @else
              <a href="/category/index" /><button type="button" class="btn btn-info btn-lg">Create Category</button>
            @endif
            </div>
          </form>
        </div>
      </div> 
    </div>
  </div>
  <!-- MODAL CREATION END -->
</div>

<script>
  $(document).ready(function() {
    $("#volume-creation").val($("#category").find("option:selected").attr("id"));
  });
  
  $("#category").change(function() {
    $("#volume").val($(this).find("option:selected").attr("id"));
  });

  $(document).on("click", ".openEditModal", function () {
    var $row = $(this).closest('tr');
    var $columns = $row.find('td');

    $columns.addClass('row-highlight');
    $("#id").val($columns[0].innerHTML);
    $("#name").val($columns[1].innerHTML);
    $("#category").val($columns[2].id);
    $("#volume").val($columns[3].innerHTML);
  });
</script>
@endsection
