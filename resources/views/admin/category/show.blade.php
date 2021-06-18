@extends('layout.admin', ['title' => 'Просмотр категории'])

@section('content')
    <h1>Просмотр категории</h1>
    <div class="row">
        <div class="col-md-6">
            <p><strong>Название:</strong> {{ $category->name }}</p>
            <p><strong>ЧПУ (англ):</strong> {{ $category->slug }}</p>
            <p><strong>Краткое описание</strong></p>
            @isset($category->content)
                <p>{{ $category->content }}</p>
            @else
                <p>Описание отсутствует</p>
            @endisset
        </div>
        <div class="col-md-6">
            @php
                if ($category->image) {
                    // $url = url('storage/catalog/category/image/' . $category->image);
                    $url = Storage::url('catalog/category/image/' . $category->image);
                } else {
                    // $url = url('storage/catalog/category/image/default.jpg');
                    $url = Storage::url('catalog/category/image/default.jpg');
                }
            @endphp
            {{--            <div class="card-header">--}}
            {{--                <p>Картинка:</p>--}}
            {{--            </div>--}}
            {{--            <div class="card-body">--}}
            {{--            <img src="{{ asset('storage/catalog/category/thumb/4nQNOmMr6WHVZjsbIlgxrUnNPJpevNgTIfUjJeDM.jpg') }}" alt="" class="img-fluid">--}}
            <img src="{{ asset($url) }}" alt="" class="img-fluid">
        </div>
        {{--    </div>--}}
    </div>
    @if ($category->children->count())
        <p><strong>Дочерние категории:</strong></p>
        <!-- Здесь таблица дочерних категорий -->
        <table class="table table-bordered">
            <tr>
                <th width="30%">Наименование</th>
                <th width="65%">Описание</th>
                <th><i class="fas fa-edit"></i></th>
                <th><i class="fas fa-trash-alt"></i></th>
            </tr>
            @include('admin.category.part.tree', ['items' => $category->children, 'level' => 0])
        </table>
    @else
        <p><strong>Нет дочерних категорий</strong></p>
    @endif
    <a href="{{ route('admin.category.edit', ['category' => $category->id]) }}"
       class="btn btn-success">
        Редактировать категорию
    </a>
    <form method="post" class="d-inline"
          action="{{ route('admin.category.destroy', ['category' => $category->id]) }}">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">
            Удалить категорию
        </button>
    </form>
@endsection
