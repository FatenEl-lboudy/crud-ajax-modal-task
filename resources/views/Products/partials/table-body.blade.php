@forelse($products as $product)
    <tr>
        <td><input type="checkbox" class="rowCheckbox" value="{{ $product->id }}"></td>
        <td>{{ $product->name }}</td>
        <td>{{ $product->category->name ?? '-' }}</td>
        <td>{{ $product->price }}</td>
        <td>{{ $product->stock_qty }}</td>
        <td>
            <a href="javascript:void(0)" class="btn-sm btn btn-info editButton mr-1" data-id="{{ $product->id }}">Edit</a>
            <a href="javascript:void(0)" class="btn-sm btn btn-danger delButton" data-id="{{ $product->id }}">Del</a>
        </td>
    </tr>
@empty
    <tr><td colspan="6" class="text-center">No products found.</td></tr>
@endforelse