<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css" />
    <title>Library details</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Library Details</a>

            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-link text-light" href="#">Library Details</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-5 col-6 md-5">
        <div class="card p-5">
            <div class="card-title">
                <h1>Add the product data here:</h1>
            </div>
            <div class="card-body">
                <form onsubmit="return false" method="POST" id="my-form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="hidden_id" id="hidden_id">
                    <input type="hidden" name="type" value="insert">
                    <input type="hidden" name="hidden_img" id="hidden_id">
                    <div class="mb-3">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" name="name" id="name"
                            placeholder="Enter name">
                    </div>

                    <div class="mb-3">
                        <label for="isbn">isbn</label>
                        <input type="text" class="form-control" name="isbn" id="isbn"
                            placeholder="Enter isbn number">
                    </div>

                    <div class="mb-3">
                        <label for="image">Image</label>
                        <input type="file" class="form-control" name="image" id="image"
                            placeholder="Enter Image">
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary mt-3" id="submit"
                            value="Add Student">Submit</button>
                    </div>
                </form>
            </div>
            <span id="output"></span>
        </div>
    </div>

    <div class="container mt-5">
        <h2>Library Table</h2>
        <table class="table table-bordered" id="table">
            <thead>
                <tr>
                    <th>Sr.no</th>
                    <th>Name</th>
                    <th>ISBN no.</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="tbody">
            </tbody>
        </table>

        <span id="output"></span>

</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>


<script>
    $(document).ready(function() {

        var table = $('#table').DataTable({
            searching: true,
            paging: true,
            pageLength: 10,
            ajax: {
                url: '/listing',
                type: 'GET',
                dataType: 'json',
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'name'
                },
        
                {
                    data: 'isbn'
                },
                {
                    data: 'image'
                },
                {
                    data: 'action'
                },
            ]
        
        });
        
        $("#my-form").submit(function(e) {
            $.ajax({
                type: "POST",
                url: "{{ route('library.libraryView') }}",
                data: new FormData(this),
                processData: false,
                contentType: false,
                cache: false,
                success: function(data) {
                    $('#my-form')[0].reset();
                    $('#output').text(data.res);
                    table.ajax.reload();
                    console.log(data);
                },

                error: function(e) {
                    console.log("error", e);
                }
            })


        })


        function listing() {
            $(document).on('click', '.edit', function() {
                let editId = this.getAttribute('id');
                $.ajax({
                    type: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'editId': editId,
                        'type': 'edit'
                    },
                    url: "{{ route('library.edit') }}",
                    success: function(data) {
                        let singleLibraryData = data.singleLibraryData
                        $('#name').val(singleLibraryData.name)
                        $('#hidden_id').val(singleLibraryData.id)
                        $('#isbn').val(singleLibraryData.isbn)
                        $('#hidden_img').val(singleLibraryData.image)
                    },
                    error: function(e) {
                        console.log("error", e);
                    }

                })
            })
        }



        $(document).on('click', '.delete', function() {
            let deleteId = this.getAttribute('id');

            $.ajax({
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    'deleteId': deleteId,
                    'type': 'delete'
                },
                url: "{{ route('library.delete') }}",
                success: function(data) {
                    $('#output').text(data.res);
                    table.ajax.reload();
                },
                error: function(e) {
                    console.log("error", e);
                }

            })
        })





    })
</script>

</html>
