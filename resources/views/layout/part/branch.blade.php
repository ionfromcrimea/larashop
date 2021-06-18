{{--<ul>--}}
{{--    @foreach ($items as $item)--}}
{{--        <li>--}}
{{--            <a href="{{ route('catalog.category', ['slug' => $item->slug]) }}">{{ $item->name }}</a>--}}
{{--            @if ($item->children->count())--}}
{{--                --}}{{--                <span class="badge badge-dark">--}}
{{--                --}}{{--                <i class="fa fa-plus"></i></span>--}}
{{--                <span class="badge badge-dark">--}}
{{--                    <i class="glyphicon glyphicon-plus"></i> <!-- бейдж с плюсом или минусом --> </span>--}}
{{--                --}}{{--                @include('layout.part.branch', ['items' => $item->children])--}}
{{--                @include('layout.part.branch', ['items' => $item->descendants])--}}
{{--            @endif--}}
{{--        </li>--}}
{{--    @endforeach--}}
{{--</ul>--}}

<ul>
    @foreach ($items->where('parent_id', $parent) as $item)
        <li>
            <a href="{{ route('catalog.category', ['slug' => $item->slug]) }}">{{ $item->name }}</a>
            @if (count($items->where('parent_id', $item->id)))
                {{--                <span class="badge badge-dark">--}}
                {{--                <i class="fa fa-plus"></i></span>--}}
                <span class="badge badge-dark">
                    <i class="glyphicon glyphicon-plus"></i> <!-- бейдж с плюсом или минусом --> </span>
                @include('layout.part.branch', ['parent' => $item->id])
            @endif
        </li>
    @endforeach
</ul>
