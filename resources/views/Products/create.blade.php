<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <title>Add Item</title>
</head>

<body>
    <!-- Modal -->
    <div class="modal fade ajax-modal" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <form id="ajaxForm">
            @csrf
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-title"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- hidden input to detect whether the request is create or update, value included=> update request -->
                        <input type="hidden" name="product_id" id="product_id">
                        <div class="form-group mb-3">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" name="name" id="name" placeholder="iPhone 15 Pro 256GB">
                            <span id="nameError" class="text-danger error-messages"></span>
                        </div>
                        <div class="form-group mb-3">
                            <label for="type">Category</label>
                            <select class="form-control" name="type" id="type">
                                <option disabled selected>Select a category</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <span id="typeError" class="text-danger error-messages"></span>
                        </div>
                        <div class="form-group mb-3">
                            <label for="price">Price</label>
                            <input type="text" class="form-control" name="price" id="price" placeholder="50000 EGP">
                            <span id="priceError" class="text-danger error-messages"></span>
                        </div>
                        <div class="form-group mb-1">
                            <label for="stock">Stock</label>
                            <input type="text" class="form-control" name="stock" id="stock" placeholder="150">
                            <span id="stockError" class="text-danger error-messages"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="saveBtn"></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="row">
        <div class="col-md-6 offset-3" style="margin-top: 100px">
            <a class="btn btn-info mb-3" data-toggle="modal" data-target="#exampleModal">Add Item</a>
            <table class="table" id="products-table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Category</th>
                        <th scope="col">Price</th>
                        <th scope="col">Stock</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#products-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("products.index") }}',
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'category.name',
                        name: 'category.name'
                    },
                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'stock_qty',
                        name: 'stock_qty'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            $('body').on('click', '[data-target="#exampleModal"]', function() {
                $('#ajaxForm')[0].reset(); // clears all input/select values
                $('#product_id').val(''); // clear the hidden id
                $('.error-messages').html(''); 
                $('#modal-title').html('Create Product');
                $('#saveBtn').html('Save Product');
            });


            $('#saveBtn').click(function() {
                $('.error-messages').html('');

                var formData = new FormData($('#ajaxForm')[0]); // Get form data

                $.ajax({
                    url: "{{ route('products.store') }}",
                    type: "POST",
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: formData,
                    success: function(response) {
                        table.draw();
                        $(".ajax-modal").modal("hide");
                        swal("Success!", response.success, "success");

                    },
                    error: function(error) {
                        if (error) {
                            console.log(error.responseJSON.errors.name);
                            $('#nameError').html(error.responseJSON.errors.name);
                            $('#typeError').html(error.responseJSON.errors.type);
                            $('#priceError').html(error.responseJSON.errors.price);
                            $('#stockError').html(error.responseJSON.errors.stock);
                        }
                    }
                });
            })
            //edit button click
            $('body').on('click', '.editButton', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ url('products') }}" + '/' + id + '/edit',
                    type: "get",
                    data: {
                        id: id,
                    },
                    success: function(response) {
                        $(".ajax-modal").modal("show");
                        $('#modal-title').html('Edit Product');
                        $('#saveBtn').html('Update Product');

                        $('#product_id').val(response.id);
                        $('#name').val(response.name);
                        $('#type').val(response.type);
                        //console.log('type value:', response.type);
                        // $('#type').empty().append('<option selected value="'+ category.id +'">'+ response.type +'</option>').selectmenu('refresh');
                        $('#price').val(response.price);
                        $('#stock').val(response.stock);
                    },
                    error: function(error) {
                        console.log(error);
                    }
                })
            });
        });
    </script>
</body>

</html>