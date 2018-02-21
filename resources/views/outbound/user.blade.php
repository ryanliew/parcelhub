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
                          @forelse($products as $product)
                              <label>Product: </label>
                              <select name="products[{{ $product->id }}][id]">
                                  <option value="{{$product->id}}">{{$product->name}}</option>
                              </select>
                              <div style="display: inline-block">
                                  <label>Quantity: </label>
                                  <input type="number" name="products[{{ $product->id }}][quantity]" value="{{ $product->total_quantity }}"/>
                              </div>
                          @empty
                              <select name="products[][id]">
                                  <option disabled>No category is found. Please click "Create Product" to create new product.</option>
                              </select>
                              <div style="display: inline-block">
                                  <label>Quantity: </label>
                                  <input type="number" name="products[][quantity]" />
                              </div>
                          @endforelse
                      </div>

                      <div>
                          <label>Courier: </label>
                          <select name="courier_id">
                              @foreach($couriers as $courier)
                                  <option value="{{ $courier->id }}"> {{ $courier->name }} </option>
                              @endforeach
                          </select>
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
                                  <input type="number" name="amount_insured" value="0">
                              </label>
                          </div>
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

<script>
    $(document).ready(function () {
        var amountOfInsurance = $('#amountOfInsurance');

        $("#insuranceYes").on('click', function () {
           if($(this).is(":checked")) {
               amountOfInsurance.show();
           }
        });

        $("#insuranceNo").on('click', function () {
            if($(this).is(":checked")) {
                amountOfInsurance.hide();
            }
        });
    });
</script>

@endsection