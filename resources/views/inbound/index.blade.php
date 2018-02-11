@extends('layout.admin.dashboard')
@section('body')
<div class="container">
  <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#inboundCreation">Create new inbound</button>
  <h2>Inbounds</h2>
  <table>
    <tr>
      <th>
        <label>ID</label>
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
        <td>{{$inbound->arrival_date}}</td>
        <td>{{$inbound->total_carton}}</td>
        <!-- <td><button type="button" class="openEditModal btn btn-info btn-lg" data-toggle="modal" data-target="#inboundEdit">Edit</button></td>
        <td><a href="delete/{{$inbound->id}}" /><button type="button" class="btn btn-info btn-lg">Delete</button></td> -->
        <td><a href="show/{{$inbound->id}}" /><button type="button" class="btn btn-info btn-lg">View</button></td>
      <tr>
    @empty
      <tr>
        <td>No result found.</td>
      <tr>
    @endforelse
  </table>

<!-- MODAL EDIT START -->
  <div class="modal fade" id="inboundEdit" role="dialog">
    <div class="modal-dialog">
    
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Inbound Edit</h4>
        </div>
        <div class="modal-body">
          <form action="update" method="post">
            <input type="hidden" name="_token" value="{{csrf_token()}}"/>
            <div>
              <label>Product: </label>
                <select id="product" name="product">
                  @forelse($products as $product)
                      <option value="{{$product->id}}">{{$product->name}}</option>
                  @empty
                    <option disabled>No category is found. Please click "Create Product" to create new product.</option>
                  @endforelse
                </select>
              </div>
            <div>
              <label>Quantity: </label>
              <input type="number" id="quantity" name="quantity" />
            </div>
            <div>
              <label>Arrival Date: </label>
              <input type="number" id="date" name="date" />
            </div>
            <div>
              <label>Total Carton: </label>
              <input type="number" id="carton" name="carton" />
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
  <div class="modal fade" id="inboundCreation" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Inbound Creation</h4>
        </div>
        <div class="modal-body">
          <form action="store" method="post">
            <input type="hidden" name="_token" value="{{csrf_token()}}"/>
            <div>
              <label>Name: </label>
              <input type="text" name="name" />
            </div>
            <div>
              <label>Product: </label>
              <select id="product" name="product">
                @forelse($products as $product)
                  <option value="{{$product->id}}">{{$product->name}}</option>
                @empty
                  <option disabled>No category is found. Please click "Create Product" to create new product.</option>
                @endforelse
              </select>
            </div>
            <div>
              <label>Quantity: </label>
              <input type="number" id="quantity" name="quantity" />
            </div>
            <div>
              <label>Arrival Date: </label>
              <input type="date" id="date" name="date" />
            </div>
            <div>
              <label>Total Carton: </label>
              <input type="number" id="carton" name="carton" />
            </div>
            <div class="modal-footer">
            @if(count($products) > 0)
              <a href="/product/index" /><button type="button" class="btn btn-info btn-lg">Create Product</button>
              <input type="submit" value="Submit">
            @else
              <a href="/product/index" /><button type="button" class="btn btn-info btn-lg">Create Product</button>
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