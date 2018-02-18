@extends('layout.admin.dashboard')
@section('body')
  <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#creation">Create new outbound</button>
  <h2>Admin Manage Outbound</h2>
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
  </table>

  <!-- MODAL CREATION START -->
  <div class="modal fade" id="creation" role="dialog">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <h4 class="modal-title">Inbound Creation</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>

              <div class="modal-body">
                  {{--<form action="store" method="post">--}}
                          {{--<input type="hidden" name="_token" value="{{csrf_token()}}"/>--}}
                          {{--<div>--}}
                              {{--<label>Name: </label>--}}
                              {{--<input type="text" name="name" />--}}
                          {{--</div>--}}
                          {{--<div>--}}
                              {{--<label>Product: </label>--}}
                              {{--<select id="product" name="product">--}}
                                  {{--@forelse($products as $product)--}}
                                      {{--<option value="{{$product->id}}">{{$product->name}}</option>--}}
                                  {{--@empty--}}
                                      {{--<option disabled>No category is found. Please click "Create Product" to create new product.</option>--}}
                                  {{--@endforelse--}}
                              {{--</select>--}}
                          {{--</div>--}}
                          {{--<div>--}}
                              {{--<label>Quantity: </label>--}}
                              {{--<input type="number" id="quantity" name="quantity" />--}}
                          {{--</div>--}}
                      {{--<div>--}}
                          {{--<label>Arrival Date: </label>--}}
                          {{--<input type="date" id="date" name="arrival_date" />--}}
                      {{--</div>--}}
                      {{--<div>--}}
                          {{--<label>Total Carton: </label>--}}
                          {{--<input type="number" id="carton" name="total_carton" />--}}
                      {{--</div>--}}
                      {{--<div class="modal-footer">--}}
                          {{--@if(count($products) > 0)--}}
                              {{--<a href="/product/index" /><button type="button" class="btn btn-info btn-lg">Create Product</button>--}}
                              {{--<input type="submit" value="Submit">--}}
                          {{--@else--}}
                              {{--<a href="/product/index" /><button type="button" class="btn btn-info btn-lg">Create Product</button>--}}
                          {{--@endif--}}
                      {{--</div>--}}
                  {{--</form>--}}
              </div>

          </div>
      </div>
  </div>

  {{--<div class="modal fade" id="inboundEdit" role="dialog">--}}
    {{--<div class="modal-dialog">--}}
    {{----}}
      {{--<div class="modal-content">--}}
        {{--<div class="modal-header">--}}
          {{--<button type="button" class="close" data-dismiss="modal">&times;</button>--}}
          {{--<h4 class="modal-title">Inbound Edit</h4>--}}
        {{--</div>--}}
        {{--<div class="modal-body">--}}
          {{--<form action="update" method="post">--}}
            {{--<input type="hidden" name="_token" value="{{csrf_token()}}"/>--}}
            {{--<div>--}}
              {{--<label>Product: </label>--}}
                {{--<select id="product" name="product">--}}
                  {{--@forelse($products as $product)--}}
                      {{--<option value="{{$product->id}}">{{$product->name}}</option>--}}
                  {{--@empty--}}
                    {{--<option disabled>No category is found. Please click "Create Product" to create new product.</option>--}}
                  {{--@endforelse--}}
                {{--</select>--}}
              {{--</div>--}}
            {{--<div>--}}
              {{--<label>Quantity: </label>--}}
              {{--<input type="number" id="quantity" name="quantity" />--}}
            {{--</div>--}}
            {{--<div>--}}
              {{--<label>Arrival Date: </label>--}}
              {{--<input type="number" id="date" name="date" />--}}
            {{--</div>--}}
            {{--<div>--}}
              {{--<label>Total Carton: </label>--}}
              {{--<input type="number" id="carton" name="carton" />--}}
            {{--</div>--}}
            {{--<input type="hidden" id="id" name="id" />--}}
            {{--<div class="modal-footer">--}}
              {{--<input type="submit" value="Submit">--}}
            {{--</div>--}}
          {{--</form>--}}
        {{--</div>--}}
      {{--</div> --}}
    {{--</div>--}}
  {{--</div>--}}
{{--<!-- MODAL EDIT END -->--}}

{{--<script>--}}
{{--$(document).on("click", ".openEditModal", function () {--}}
    {{--var $row = $(this).closest('tr');--}}
    {{--var $columns = $row.find('td');--}}

    {{--$columns.addClass('row-highlight');--}}
    {{--$("#id").val($columns[0].innerHTML);--}}
    {{--$("#name").val($columns[1].innerHTML);--}}
    {{--$("#volume").val($columns[2].innerHTML);--}}
{{--});--}}
{{--</script>--}}

@endsection