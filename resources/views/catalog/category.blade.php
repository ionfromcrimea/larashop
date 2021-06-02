@extends('layout.site')

@section('content')
    <h1>{{ $category->name }}</h1>

    <p>{{ $category->content }}</p>

    <div class="row">
        @foreach ($products as $product)
            @include('catalog.part.product', ['product' => $product])
        @endforeach
    </div>
@endsection
