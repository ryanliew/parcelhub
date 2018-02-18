@extends('layout.admin.dashboard')
@section('body')
  <h2>User Create/Edit Outbound</h2>

  <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#creation">Create new outbound</button>

  <table class="table">
      <tr>
          <th>ID</th>
          <th>Arrival Date</th>
          <th>Total carton</th>
      </tr>
  </table>

  <!-- MODAL CREATION START -->
  <div class="modal fade" id="creation" role="dialog">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <h4 class="modal-title">Outbound Creation</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>

              <form action="{{ route('outbound.store') }}" method="post">
                  <div class="modal-body">

                      {{ csrf_field() }}

                      <div>
                          <label>Recipient Name: </label>
                          <input type="text" name="recipient_name" />
                      </div>

                      <div>
                          <label>Recipient Address: </label>
                          <input type="text" name="recipient_address" />
                      </div>

                      <div>
                          <label>Product: </label>
                          <select name="product[]">
                              @forelse($products as $product)
                                  <option value="{{$product->id}}">{{$product->name}}</option>
                              @empty
                                  <option disabled>No category is found. Please click "Create Product" to create new product.</option>
                              @endforelse
                          </select>
                          <div style="display: inline-block">
                              <label>Quantity: </label>
                              <input type="number" name="quantity" />
                          </div>
                      </div>
                      <div>
                          <label>Product: </label>
                          <select name="product[]">
                              @forelse($products as $product)
                                  <option value="{{$product->id}}">{{$product->name}}</option>
                              @empty
                                  <option disabled>No category is found. Please click "Create Product" to create new product.</option>
                              @endforelse
                          </select>
                          <div style="display: inline-block">
                              <label>Quantity: </label>
                              <input type="number" name="quantity" />
                          </div>
                      </div>

                      <div>
                          <label>Courier: </label>
                          <select name="courier">
                              @foreach($couriers as $courier)
                                  <option value="{{ $courier->id }}"> {{ $courier->name }} </option>
                              @endforeach
                          </select>
                      </div>

                      <div>
                          <label>Dangerous: </label>
                          <label>
                              Yes
                              <input type="radio" name="dangerous" value="yes">
                          </label>
                          <label>
                              No
                              <input type="radio" name="dangerous" value="no">
                          </label>
                      </div>

                      <div>
                          <label>Insurance: </label>
                          <label>
                              Yes
                              <input type="radio" id="insuranceYes" name="insurance" value="yes">
                          </label>
                          <label>
                              No
                              <input type="radio" id="insuranceNo" name="insurance" value="no">
                          </label>
                          <div id="amountOfInsurance" style="display: none">
                              <label>
                                  Amount of Insurance:
                                  <input type="number" name="amount_insured">
                              </label>
                          </div>
                      </div>

                      <div>
                          <label>Arrival Date: </label>
                          <input type="date" name="arrival_date" />
                      </div>

                      <div>
                          <label>Total Carton: </label>
                          <input type="number" id="carton" name="total_carton" />
                      </div>
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

<script>
    $(document).ready(function () {
        var amountOfInsurance = $('#amountOfInsurance');

        $('#insuranceYes').click(function () {
           if($(this).is(":checked")) {
               amountOfInsurance.show();
           }
        });

        $('#insuranceNo').click(function () {
            if($(this).is(":checked")) {
                amountOfInsurance.hide();
            }
        });
    });

    {{--var $row = $(this).closest('tr');--}}
    {{--var $columns = $row.find('td');--}}

    {{--$columns.addClass('row-highlight');--}}
    {{--$("#id").val($columns[0].innerHTML);--}}
    {{--$("#name").val($columns[1].innerHTML);--}}
    {{--$("#volume").val($columns[2].innerHTML);--}}
{{--});--}}
</script>

@endsection