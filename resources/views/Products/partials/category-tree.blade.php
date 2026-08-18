<ul class="list-unstyled {{ isset($isChild) ? 'pl-3' : '' }}">
    @foreach ($categories as $category)
        <li class="mb-1">
            <a href="javascript:void(0)" class="category-filter" data-id="{{ $category->id }}">
                {{ $category->name }}
            </a>

            @if ($category->children->count())
                @include('products.partials.category-tree', [
                    'categories' => $category->children,
                    'isChild' => true,
                ])
            @endif
        </li>
    @endforeach
</ul>