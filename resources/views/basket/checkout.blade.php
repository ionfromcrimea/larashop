@extends('layout.site', ['title' => 'Оформить заказ'])

@section('content')
    <h1 class="mb-4">Оформить заказ</h1>
    <form method="post" action="{{ route('basket.saveorder') }}">
        @csrf
        <div class="form-group">
            <input type="text" class="form-control" name="name" placeholder="Имя, Фамилия"
                   value="{{ old('name') ?? '' }}">
{{--            required maxlength="255" value="{{ old('name') ?? '' }}">--}}
        </div>
        <div class="form-group">
            <input type="email" class="form-control" name="email" placeholder="Адрес почты"
                   value="{{ old('email') ?? '' }}">
{{--            required maxlength="255" value="{{ old('email') ?? '' }}">--}}
        </div>
        <div class="form-group">
            <input type="text" class="form-control" name="phone" placeholder="Номер телефона"
                   value="{{ old('phone') ?? '' }}">
{{--            required maxlength="255" value="{{ old('phone') ?? '' }}">--}}
        </div>
        <div class="form-group">
            <input type="text" class="form-control" name="address" placeholder="Адрес доставки"
                   value="{{ old('address') ?? '' }}">
{{--            required maxlength="255" value="{{ old('address') ?? '' }}">--}}
        </div>
        <div class="form-group">
            <textarea class="form-control" name="comment" placeholder="Комментарий"
                      rows="2">{{ old('comment') ?? '' }}</textarea>
{{--            maxlength="255" rows="2">{{ old('comment') ?? '' }}</textarea>--}}
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-success">Оформить</button>
        </div>
    </form>
@endsection
