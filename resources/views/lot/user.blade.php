@extends('layout.admin.dashboard')
@section('body')
    <h2>Purchase Lots</h2>

    <form action="{{ action('LotController@purchase') }}" method="post">

        {{ csrf_field() }}

        <table class="table">
            <tr>
                <th>Name</th>
                <th>Categories</th>
                <th>Volume</th>
                <th>Action</th>
            </tr>
            <tr class="child">
                <td>
                    <label>
                        <input class="name" type="text" name="lots[0][name]">
                    </label>
                </td>
                <td>
                    <label>
                        <select class="categories" name="lots[0][categories]"></select>
                    </label>
                </td>
                <td>
                    <label>
                        <input class="volume" type="text" name="lots[0][volume]" readonly>
                    </label>
                </td>
                <td>
                    <input class="add" type="button" value="add">
                </td>
            </tr>
        </table>
        <input type="submit" name="purchase" id="purchase" value="Purchase">
    </form>

    <script>
        var categories = {};
        var dropdown = $('.categories');

        $(document).ready(function () {

            $.getJSON('{{ route('json.categories') }}', function (data) {
                $.each(data, function(k, v) {

                    categories[v.name] = v.volume;

                    dropdown.append($('<option>', {
                        value: v.id,
                        text: v.name
                    }));

                    dropdown.closest('td').next('td').find('.volume').val(v.volume);

                });
            });

            $(".categories").on('change', function () {

                var selected = $(this).find("option:selected").text();

                $(this).closest('td').next('td').find('.volume').val(categories[selected]);

            });

            $('.add').on('click', function () {
                $last = $('.table tr:last');
                $clone = $last.clone(true);

                // var n = ;
                // alert(n);

                $clone.find("[name^='lots[']").each(function () {

                    var name = $(this).prop('name');
                    var suffix = name.match(/\d+/);

                    name = name.replace(suffix, parseInt(suffix, 10) + 1);

                    $(this).prop('name', name);
                });

                $last.after($clone);
            })
        });
    </script>
@endsection
