@extends('layout.admin.dashboard')
@section('body')
<div class="container">
  <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#productCreation">Create new product</button>
  <h2>Products</h2>
  <table>
    <tr>
      <th>
        <label>Lot</label>
      </th>
      <th>
        <label>Name</label>
      </th>
      <th>
        <label>Size(cm3)</label>
      </th>
      <th>
        <label>SKU</label>
      </th>
      <th>
        <label>Photo</label>
      </th>
      <th>
        <label>Edit</label>
      </th>
      <th>
        <label>Delete</label>
      </th>
    </tr>
    @forelse($products as $product)
      <tr>
        @if(count($product->lot_id) > 0)
          <td>{{$product->lot->name}}</td>
        @else
          <td>Product is not assigned to lot yet.</td>
        @endif
        <td>{{$product->name}}</td>
        <td>{{($product->height)*($product->width)*($product->length)}}</td>
        <td>{{$product->sku}}</td>
        @if(count($product->picture) > 0)
          <td id="{{$product->picture}}"><img src="/images/{{$product->picture}}" height="200"></td>
        @else
          <td>No image is uploaded for this product.</td>
        @endif
        <td style="display:none">{{$product->height}}</td>
        <td style="display:none">{{$product->width}}</td>
        <td style="display:none">{{$product->length}}</td>
        <td style="display:none">{{$product->id}}</td>
        <td><button type="button" class="openEditModal btn btn-info btn-lg" data-toggle="modal" data-target="#productEdit">Edit</button></td>
        <td><a href="delete/{{$product->id}}" /><button type="button" class="btn btn-info btn-lg">Delete</button></td>
      <tr>
    @empty
      <tr>
        <td>No result found.</td>
      <tr>
    @endforelse
  </table>

<!-- MODAL EDIT START -->
  <div class="modal fade" id="productEdit" role="dialog">
    <div class="modal-dialog">
    
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Product Edit</h4>
        </div>
        <div class="modal-body">
          <form action="update" method="post" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{csrf_token()}}"/>
            <div>
              <label>Name: </label>
              <input type="text" id="name" name="name" />
            </div>
            <div>
              <label>Height(cm): </label>
              <input type="number" id="height" name="height" step="0.01" />
            </div>
            <div>
              <label>Width(cm): </label>
              <input type="number" id="width" name="width" step="0.01" />
            </div>
            <div>
              <label>Length(cm): </label>
              <input type="number" id="length" name="length" step="0.01" />
            </div>
            <div>
              <label>SKU: </label>
              <input type="text" id="sku" name="sku" placeholder="e.g SKU001" />
            </div>
            <div>
              <label>Current Picture: </label>
              <div><img src="" height="200" id="showPicture"></div>
              <input type="file" name="picture" id="picture">
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
  <div class="modal fade" id="productCreation" role="dialog">
    <div class="modal-dialog">
    
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Product Creation</h4>
        </div>
        <div class="modal-body">
          <form action="store" method="post" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{csrf_token()}}"/>
            <div>
          		<label>Name: </label>
          		<input type="text" name="name" />
          	</div>
          	<div>
          		<label>Height: </label>
          		<input type="number" name="height" step="0.01" />
          	</div>
            <div>
              <label>Width: </label>
              <input type="number" name="width" step="0.01" />
            </div>
            <div>
              <label>Length: </label>
              <input type="number" name="length" step="0.01"/>
            </div>
            <div>
              <label>SKU: </label>
              <input type="text" name="sku" placeholder="e.g SKU001" />
            </div>
            <div>
              <label>Picture: </label>
              <input type="file" name="picture" id="file">
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
    $("#id").val($columns[8].innerHTML);
    $("#name").val($columns[1].innerHTML);
    $("#sku").val($columns[3].innerHTML);
    $("#height").val($columns[5].innerHTML);
    $("#width").val($columns[6].innerHTML);
    $("#length").val($columns[7].innerHTML);
    $('#showPicture').attr('src', /images/+$columns[4].id);
});
</script>

@endsection