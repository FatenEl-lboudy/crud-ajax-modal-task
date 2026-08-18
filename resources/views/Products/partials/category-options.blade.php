@foreach($categories as $category)
    <option value="{{ $category->id }}" {{ ($selectedId ?? null) == $category->id ? 'selected' : '' }}>
        {{ str_repeat('— ', $depth ?? 0) }}{{ $category->name }}
    </option>

    @if($category->children->count())
        @include('products.partials.category-options', [
            'categories' => $category->children,
            'depth' => ($depth ?? 0) + 1
        ])
    @endif
@endforeach