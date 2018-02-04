@extends('layout.admin.dashboard')
@section('body')
HERE will be the list of existing log.

<div class="container">
  <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#logCreation">Create new lot</button>

  <div class="modal fade" id="logCreation" role="dialog">
    <div class="modal-dialog">
    
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Lot Creation</h4>
        </div>
        <div class="modal-body">
          <form action="/create" method="post">
          	<div>
          		<label>Name: </label>
          		<input type="text" name="name" />
          	</div>
          	<div>
          		<label>Volume: </label>
          		<input type="text" name="volume" />
          	</div>
          	<div>
          		<label>Category: </label>
          		<select>
          			<option>testing</option>
          			<option>testing1</option>
          		</select>
          	</div>
          	<div class="modal-footer">
          		<input type="submit" value="Submit">
          	</div>
          </form>
        </div>
      </div> 
    </div>
  </div>
</div>
@endsection