@extends('layout.site', ['title' => 'Страница не найдена'])

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mt-4 mb-4">
                <div class="card-header">
                    <h4>Страница не найдена</h4>
                </div>
                <div class="card-body">
                    <img src="{{ asset('img/png404-2.png') }}" alt="" class="img-fluid">
{{--                    <img src="{{ asset('storage/catalog/category/image/4nQNOmMr6WHVZjsbIlgxrUnNPJpevNgTIfUjJeDM.jpg ') }}" alt="" class="img-fluid">--}}
                </div>
                <div class="card-footer">
{{--                    <p>Запрошенная страница не найдена.</p>--}}
                    <h3>{{ isset($exception) ? $exception->getMessage() : "" }}</h3>
                </div>
            </div>
        </div>
    </div>
@endsection
