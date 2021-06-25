@extends('layout.site', ['title' => $brand->name])

@section('content')
    <h1>{{ $brand->name }}</h1>
    <p>{{ $brand->content }}</p>
    <h5 class="bg-info text-white p-1 mb-4">Товары бренда</h5>
    <div class="row">
        {{--        @foreach ($brand->products as $product)--}}
        {{--            @include('catalog.part.product')--}}
        {{--        @endforeach--}}
        @foreach ($products as $product)
            @include('catalog.part.product', ['product' => $product])
        @endforeach
    </div>
    {{ $products->links() }}
@endsection
