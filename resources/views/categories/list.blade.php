@extends('custom.page')



@section('content_header')
    <div class="card card-outline card-primary">
        <div class="card-body">
            {{ Diglactic\Breadcrumbs\Breadcrumbs::render('categories_list') }}
        </div>
    </div>
@endsection



@section('content')
    <div class="card card-outline card-warning">
        @php
            $heads = [
                'ID',
                'Name',
                ['label' => 'URL', 'width' => 40],
                'Order'
            ];

            $config = [
                'ajax' => route('fetch_categories_api'),
                'processing' => true,
                'order' => [[3, 'asc']],
                'columns' => [
                    ['data' => 'id'],
                    ['data' => 'name'],
                    ['data' => 'url', 'orderable' => false],
                    ['data' => 'order', 'className' => 'reorder'],
                    //['data' => 'actions', 'orderable' => false]
                ],
                'rowReorder' => [
                    'dataSrc' => 'order',
                    'selector' => 'td.reorder'
                ],
                'bPaginate' => false,
                'searching' => false,
                'bLengthChange' => false,
                'bInfo' => false
            ];
        @endphp

        <x-adminlte-datatable id="table2" :heads="$heads" head-theme="dark" :config="$config"
                              striped hoverable bordered compressed/>
    </div>
@endsection



@section('js')
    <script>
        $(document).ready(function () {
            $('#table2').on( 'row-reordered.dt', function ( e ) {
                let categories = [];
                $(e.target).DataTable().rows().data().each(function (cat) {
                    categories.push({"id": cat.id, "order": cat.order});
                });
                $.post('{{ route('update_categories_order') }}', {categories: categories});
            });
        });
    </script>
@endsection
