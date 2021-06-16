@php $level++ @endphp
@foreach($items as $item)
    <option value="{{ $item->id }}"
        @isset($category)
        @if($item->id == $category->parent_id)
        selected
        @endif
        @endisset
    >
        @if ($level) {!! str_repeat('&nbsp;&nbsp;&nbsp;', $level) !!}  @endif {{ $item->name }}
    </option>
    @if ($item->children->count())
        @include('admin.category.part.branch', ['items' => $item->children, 'level' => $level])
    @endif
@endforeach

