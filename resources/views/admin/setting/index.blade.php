@extends('layout.admin.dashboard')
@section('body')
<div class="container">
  <h2>Settings</h2>
  <table>
    <tr>
      <th>
        <label>Setting</label>
      </th>
      <th>
        <label>Value</label>
      </th>
    </tr>
	<tr>
	  <td id="rental_duration">Rental Duration</td>
      <td>{{$rental_duration}}</td>
	  <td><button type="button" class="openEditModal btn btn-info btn-lg" data-toggle="modal" data-target="#settingEdit">Edit</button></td>
	<tr>
	<tr>
	  <td id="days_before_order">Days before order</td>
      <td>{{$days_before_order}}</td>
	  <td><button type="button" class="openEditModal btn btn-info btn-lg" data-toggle="modal" data-target="#settingEdit">Edit</button></td>
	<tr>
  </table>

<!-- MODAL EDIT START -->
  <div class="modal fade" id="settingEdit" role="dialog">
    <div class="modal-dialog">
    
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Setting Edit</h4>
        </div>
        <div class="modal-body">
          <form action="update" method="post">
            <input type="hidden" name="_token" value="{{csrf_token()}}"/>
            <div>
              <label id="displaySetting"></label>
              <input type="number" id="setting" name="setting" />
            </div>
            <input type="hidden" id="key" name="key" />
            <div class="modal-footer">
              <input type="submit" value="Submit">
            </div>
          </form>
        </div>
      </div> 
    </div>
  </div>
<!-- MODAL EDIT END -->
</div>

<script>
$(document).on("click", ".openEditModal", function () {
    var $row = $(this).closest('tr');
    var $columns = $row.find('td');
    $("#displaySetting").empty();
    $columns.addClass('row-highlight');
    $("#key").val($columns[0].id);
    $("#displaySetting").append($columns[0].innerHTML+":");
    $("#setting").val($columns[1].innerHTML);
});
</script>

@endsection