@extends('layout.site', ['title' => 'Оформить заказ'])

@section('content')
    <h1 class="mb-4">Оформить заказ</h1>
    @if($profiles)
        <form action="{{ route('basket.checkout') }}" method="get" id="profiles">
            <div class="form-group">
                <select name="profile_id" class="form-control">
                    <option value="0">Выберите профиль</option>
                    @foreach($profiles as $profile)
                        <option value="{{ $profile->id }}"
                                @if (isset($currentProfile) and $profile->id == $currentProfile->id) selected @endif>
                            {{ $profile->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Выбрать</button>
            </div>
        </form>
        <br><br>
    @endif
    <form method="post" action="{{ route('basket.saveorder') }}">
        @csrf
        <div class="form-group">
            <input type="text" class="form-control" name="name" placeholder="Имя, Фамилия"
                   value="{{ old('name') ?? $currentProfile->name ?? '' }}">
            {{--            required maxlength="255" value="{{ old('name') ?? '' }}">--}}
        </div>
        <div class="form-group">
            <input type="email" class="form-control" name="email" placeholder="Адрес почты"
                   value="{{ old('email') ?? $currentProfile->email ?? '' }}">
            {{--            required maxlength="255" value="{{ old('email') ?? '' }}">--}}
        </div>
        <div class="form-group">
            <input type="text" class="form-control" name="phone" placeholder="Номер телефона"
                   value="{{ old('phone') ?? $currentProfile->phone ?? '' }}">
            {{--            required maxlength="255" value="{{ old('phone') ?? '' }}">--}}
        </div>
        <div class="form-group">
            <input type="text" class="form-control" name="address" placeholder="Адрес доставки"
                   value="{{ old('address') ?? $currentProfile->adress ?? '' }}">
            {{--            required maxlength="255" value="{{ old('address') ?? '' }}">--}}
        </div>
        <div class="form-group">
            <textarea class="form-control" name="comment" placeholder="Комментарий"
                      rows="2">{{ old('comment') ?? $currentProfile->comment ?? '' }}</textarea>
            {{--            maxlength="255" rows="2">{{ old('comment') ?? '' }}</textarea>--}}
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-success">Оформить</button>
        </div>
    </form>
@endsection

