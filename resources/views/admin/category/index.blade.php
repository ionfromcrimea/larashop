@extends('layout.admin', ['title' => 'Все категории каталога'])

@section('content')
    <h1>Все категории</h1>
    <table class="table table-bordered">
        <tr>
            <th width="30%">Наименование</th>
            <th width="65%">Описание</th>
            <th><i class="fas fa-edit"></i></th>
            <th><i class="fas fa-trash-alt"></i></th>
        </tr>
{{--        @include('admin.category.part.tree', ['items' => $roots, 'level' => -1])--}}
        @include('admin.category.part.tree', ['level' => -1, 'parent' => 0])
    </table>
    <div>
        <a href="{{ route('admin.category.create') }}"
           class="btn btn-dark">Новая категория</a>
    </div>

@endsection
