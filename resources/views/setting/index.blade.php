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
    @foreach($settings as $key => $setting)
      <tr>
        <td>{{ $setting->name }}</td>
        <td>{{ $setting->value }}</td>
        <td><button type="button" class="openEditModal btn btn-info btn-lg" data-toggle="modal" data-target="#edit_{{ $key }}">Edit</button></td>
      <tr>
    @endforeach
  </table>

  @foreach($settings as $key => $setting)
    <div class="modal fade" id="edit_{{ $key }}" role="dialog">
      <div class="modal-dialog">

        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Setting Edit</h4>
          </div>
          <div class="modal-body">

            <form action="{{ route('setting.update', ['id' => $setting->id]) }}" method="post">

              {{ csrf_field() }}

              <div>
                <label>
                  {{ $setting->name }}
                  <input type="number" name="setting_value" value="{{ $setting->value }}"/>
                </label>
              </div>

              <div class="modal-footer">
                <input type="submit" value="Submit">
              </div>

            </form>

          </div>
        </div>
      </div>
    </div>
  @endforeach
<!-- MODAL EDIT END -->
</div>

<script>
// $(document).on("click", ".openEditModal", function () {
//     var $row = $(this).closest('tr');
//     var $columns = $row.find('td');
//     $("#displaySetting").empty();
//     $columns.addClass('row-highlight');
//     $("#key").val($columns[0].id);
//     $("#displaySetting").append($columns[0].innerHTML+":");
//     $("#setting").val($columns[1].innerHTML);
// });
</script>

@endsection