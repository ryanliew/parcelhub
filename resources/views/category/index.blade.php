@extends('layout.admin.dashboard')
@section('body')
HERE will be the list of existing category for lot.

<div class="container">
  <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#logCreation">Create new category for lot</button>

  <div class="modal fade" id="logCreation" role="dialog">
    <div class="modal-dialog">
    
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Category Creation</h4>
        </div>
        <div class="modal-body">
          <form action="store" method="post">
          	<div>
          		<label>Name: </label>
          		<input type="text" name="name" />
          	</div>
          	<div>
          		<label>Width(cm): </label>
          		<input type="text" name="width" />
          	</div>
            <div>
              <label>Height(cm): </label>
              <input type="text" name="height" />
            </div>
            <div>
              <label>Length(cm): </label>
              <input type="text" name="length" />
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