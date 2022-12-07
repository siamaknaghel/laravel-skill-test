<!DOCTYPE html>
<html lang="en">

<head>
    <title>Laravel Product Manager</title>

    <meta charset="utf-8">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="app-url" content="{{ url('/') }}">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="{{asset('/js/jquery.min.js')}}"></script>

    <link href="{{asset('/css/bootstrap.min.css')}}" rel="stylesheet">

    <script src="{{asset('/js/bootstrap.bundle.min.js')}}" ></script>


</head>

<body>
    <div class="container">
        <h2 class="text-center mt-5 mb-3">Laravel Product Manager with Ajax and MySQL</h2>
        <div class="card mb-2">
            <div class="card-header">
                <h3 id="form-title">Create New Product</h3>
            </div>
            <div class="card-body">
                <form>
                    <div class="row">
                        <input type="hidden" name="update_id" id="update_id">
                        <div class="form-group col-md-4">
                            <label for="name">Product Name</label>
                            <input type="text" class="form-control" id="product_name" name="product_name">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="name">Quantity</label>
                            <input type="text" class="form-control" id="quantity" name="quantity">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="name">Price</label>
                            <input type="text" class="form-control" id="price" name="price">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-outline-primary mt-3" id="save-Product-btn">Save
                        Product</button>
                    <button type="submit" class="btn btn-outline-danger mt-3 d-none" id="edit-cancel">cancel</button>


                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div id="alert-div">

                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>ِDateTime</th>
                                <th>Total</th>
                                <th width="240px">Action</th>
                            </tr>
                        </thead>
                        <tbody id="Products-table-body">

                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- view record modal -->
    <div class="modal" tabindex="-1" id="view-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Product Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <b>Product Name:</b>
                    <p id="product-name-info"></p>
                    <b>Quantity:</b>
                    <p id="quantity-info"></p>
                    <b>Price:</b>
                    <p id="price-info"></p>
                    <b>Date Submitted:</b>
                    <p id="created-at-info"></p>
                    <b>Date Updated:</b>
                    <p id="updated-at-info"></p>

                </div>
            </div>
        </div>
    </div>


    <script type="text/javascript">
        showAllProducts();

        /*
            This function will get all the Product records
        */
        function formatDateTime(productdate) {
            let date = productdate.getFullYear()+ "-" + productdate.getMonth() + "-" + productdate.getDate() ;
            let time = productdate.toLocaleTimeString().toLowerCase();

            return date + " at " + time;
        }

        function showAllProducts() {
            let url = $('meta[name=app-url]').attr("content") + "/productsmysql";
            $.ajax({
                url: url,
                type: "GET",
                success: function(response) {
                    $("#Products-table-body").html("");
                    let Products = response.products;
                    let total = 0;
                    for (var i = 0; i < Products.length; i++) {
                        let showBtn = '<button ' +
                            ' class="btn btn-outline-info" ' +
                            ' onclick="showProduct(' + Products[i].id + ')">Show' +
                            '</button> ';
                        let editBtn = '<button ' +
                            ' class="btn btn-outline-success" ' +
                            ' onclick="editProduct(' + Products[i].id + ')">Edit' +
                            '</button> ';
                        let deleteBtn = '<button ' +
                            ' class="btn btn-outline-danger" ' +
                            ' onclick="destroyProduct(' + Products[i].id + ')">Delete' +
                            '</button>';

                        let ProductRow = '<tr>' +
                            '<td>' + Products[i].product_name + '</td>' +
                            '<td>' + Products[i].quantity + '</td>' +
                            '<td>' + Products[i].price + '</td>' +
                            '<td>' + formatDateTime(new Date(Products[i].created_at)) + '</td>' +
                            '<td>' + Products[i].quantity * Products[i].price + '</td>' +
                            '<td>' + showBtn + editBtn + deleteBtn + '</td>' +
                            '</tr>';
                        $("#Products-table-body").append(ProductRow);
                        total += Products[i].quantity * Products[i].price;
                    }
                    let ProductRow = '<tr>' +
                        '<td></td>' +
                        '<td></td>' +
                        '<td></td>' +
                        '<td></td>' +
                        '<td>' + total + '</td>' +
                        '<td></td>' +
                        '</tr>';
                    $("#Products-table-body").append(ProductRow);

                },
                error: function(response) {
                    console.log(response.responseJSON)
                }
            });
        }

        /*
            check if form submitted is for creating or updating
        */
        $("#save-Product-btn").click(function(event) {
            event.preventDefault();
            if ($("#update_id").val() == null || $("#update_id").val() == "") {
                storeProduct();
            } else {
                updateProduct();
            }
        });
        /*
            cancel edit form
        */
        $("#edit-cancel").click(function(event) {
            event.preventDefault();
            $("#update_id").val("");
            $("#product_name").val("");
            $("#quantity").val("");
            $("#price").val("");
            $("#form-title").html("Create New Product");
            $("#save-Product-btn").html("Save Product");
            $(this).addClass("d-none");
        });

        /*
            submit the form and will be stored to the database
        */
        function storeProduct() {
            $("#save-Product-btn").prop('disabled', true);
            let url = $('meta[name=app-url]').attr("content") + "/productsmysql";

            let data = {
                product_name: $("#product_name").val(),
                quantity: $("#quantity").val(),
                price: $("#price").val(),
            };
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: "POST",
                data: data,
                success: function(response) {
                    $("#save-Product-btn").prop('disabled', false);
                    let successHtml =
                        '<div class="alert alert-success" role="alert"><b>Product Created Successfully</b></div>';
                    $("#alert-div").html(successHtml);
                    $("#product_name").val("");
                    $("#quantity").val("");
                    $("#price").val("");
                    showAllProducts();
                },
                error: function(response) {
                    $("#save-Product-btn").prop('disabled', false);

                    /*
                            show validation error
                                        */
                    if (typeof response.responseJSON.errors !== 'undefined') {
                        let errors = response.responseJSON.errors;
                        let descriptionValidation = "";
                        if (typeof errors.description !== 'undefined') {
                            descriptionValidation = '<li>' + errors.description[0] + '</li>';
                        }
                        let nameValidation = "";
                        if (typeof errors.name !== 'undefined') {
                            nameValidation = '<li>' + errors.name[0] + '</li>';
                        }

                        let errorHtml = '<div class="alert alert-danger" role="alert">' +
                            '<b>Validation Error!</b>' +
                            '<ul>' + nameValidation + descriptionValidation + '</ul>' +
                            '</div>';
                        $("#error-div").html(errorHtml);
                    }
                }
            });
        }


        /*
            edit record function
            it will get the existing value and show the Product form
        */
        function editProduct(id) {
            let url = $('meta[name=app-url]').attr("content") + "/productsmysql/" + id;
            $.ajax({
                url: url,
                type: "GET",
                success: function(response) {
                    let Product = response.product;
                    $("#alert-div").html("");
                    $("#error-div").html("");
                    $("#update_id").val(Product.id);
                    $("#product_name").val(Product.product_name);
                    $("#quantity").val(Product.quantity);
                    $("#price").val(Product.price);
                    $("#form-title").html("Edit Product");
                    $("#save-Product-btn").html("Edit Product");
                    $("#edit-cancel").removeClass("d-none");
                },
                error: function(response) {
                    console.log(response.responseJSON)
                }
            });
        }

        /*
            sumbit the form and will update a record
        */
        function updateProduct() {
            $("#save-Product-btn").prop('disabled', true);
            let url = $('meta[name=app-url]').attr("content") + "/productsmysql/" + $("#update_id").val();
            let data = {
                id: $("#update_id").val(),
                product_name: $("#product_name").val(),
                quantity: $("#quantity").val(),
                price: $("#price").val(),
            };
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: "PUT",
                data: data,
                success: function(response) {
                    $("#save-Product-btn").prop('disabled', false);
                    let successHtml =
                        '<div class="alert alert-success" role="alert"><b>Product Updated Successfully</b></div>';
                    $("#alert-div").html(successHtml);
                    $("#update_id").val("");
                    $("#product_name").val("");
                    $("#quantity").val("");
                    $("#price").val("");
                    $("#form-title").html("Create New Product");
                    $("#save-Product-btn").html("Save Product");
                    $("#edit-cancel").addClass("d-none");
                    showAllProducts();
                },
                error: function(response) {
                    /*
                            show validation error
                                        */
                    $("#save-Product-btn").prop('disabled', false);
                    if (typeof response.responseJSON.errors !== 'undefined') {
                        console.log(response)
                        let errors = response.responseJSON.errors;
                        let descriptionValidation = "";
                        if (typeof errors.description !== 'undefined') {
                            descriptionValidation = '<li>' + errors.description[0] + '</li>';
                        }
                        let nameValidation = "";
                        if (typeof errors.name !== 'undefined') {
                            nameValidation = '<li>' + errors.name[0] + '</li>';
                        }

                        let errorHtml = '<div class="alert alert-danger" role="alert">' +
                            '<b>Validation Error!</b>' +
                            '<ul>' + nameValidation + descriptionValidation + '</ul>' +
                            '</div>';
                        $("#error-div").html(errorHtml);
                    }
                }
            });
        }

        /*
            get and display the record info on modal
        */
        function showProduct(id) {
            $("#product-name-info").html("");
            $("#quantity-info").html("");
            $("#price-info").html("");
            $("#created-at-info").html("");
            $("#updated-at-info").html("");
            let url = $('meta[name=app-url]').attr("content") + "/productsmysql/" + id + "";
            $.ajax({
                url: url,
                type: "GET",
                success: function(response) {
                    let Product = response.product;
                    $("#product-name-info").html(Product.product_name);
                    $("#quantity-info").html(Product.quantity);
                    $("#price-info").html(Product.price);
                    $("#created-at-info").html(formatDateTime(new Date(Product.created_at)));
                    $("#updated-at-info").html(formatDateTime(new Date(Product.updated_at)));
                    $("#view-modal").modal('show');

                },
                error: function(response) {
                    console.log(response.responseJSON)
                }
            });
        }

        /*
            delete record function
        */
        function destroyProduct(id) {
            let url = $('meta[name=app-url]').attr("content") + "/productsmysql/" + id;
            let data = {
                product_name: $("#product_name").val(),
                quantity: $("#quantity").val(),
                price: $("#price").val(),
            };
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: "DELETE",
                data: data,
                success: function(response) {
                    let successHtml =
                        '<div class="alert alert-success" role="alert"><b>Product Deleted Successfully</b></div>';
                    $("#alert-div").html(successHtml);
                    showAllProducts();
                },
                error: function(response) {
                    console.log(response.responseJSON)
                }
            });
        }
    </script>
</body>

</html>
