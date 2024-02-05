<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.0/dropzone.min.css">

    <title>Library details</title>

    <style>
        .imglist {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 15px;
            flex-wrap: wrap;
        }

        .image {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 10px;
            margin: 27px;
        }
    </style>
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
                    <input type="hidden" name="hidden_img" id="hidden_img" value=" ">
                    <input type="hidden" name="folder" id="folder" value=" ">
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


                    {{-- 
                    <div class="mb-3 col-3">
                        <label>Select the Status </label>
                        <select class="form-select">
                            <option>Active</option>
                            <option>Inactive</option>
                        </select>
                    </div> --}}


                    <div id="dropzoneDragArea" class="dropzone mb-5 rounded">
                        <div class="dz-message"><span>Drop files here or click to upload.</span></div>
                        <div class="dropzone-previews"></div>
                    </div>



                    <div class="imglist" id="dropzoneImg">

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.0/dropzone.js"></script>

<script>
    Dropzone.autoDiscover = false;

    $(document).ready(function() {
        let uploadedFiles = []
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var documentDropzone = new Dropzone("div#dropzoneDragArea", {
            paramName: "file",
            url: "{{ route('dropzone.upload') }}",
            acceptedFiles: ".jpeg,.jpg,.png,.gif",
            clickable: true,
            maxFilesize: 10,
            uploadMultiple: true,
            parallelUploads: 10,

            addRemoveLinks: true,
            headers: {
                'x-csrf-token': CSRF_TOKEN,
            },
            init: function() {
                this.on("addedfile", function(file) {


                    var dropzone = this;
                    clearDropzone = function() {
                        dropzone.removeAllFiles(true);
                    };
                });



                this.on("sending", function(file, xhr, formData) {
                    let hidden_id = $('#hidden_id').val();
                    formData.append("id", hidden_id)
                });


                this.on("removedfile", function(file) {

                });

                this.on("successmultiple", function(file, responseText) {


                    let dropzoneImg = responseText.allimg;

                    let dropzoneImgfinal = dropzoneImg;

                    let editImg = $('#hidden_img').val();

                    let editImgLength = editImg.length;

                    let hiddenImg = $('#hidden_img').val();

                    // hidden Img set condition

                    if (editImgLength > 1) {
                        $('#hidden_img').val(dropzoneImg + ',' + editImg);
                    } else {
                        $('#hidden_img').val(dropzoneImg);
                    }

                    $("#allimg").val(responseText.allimg);

                    $foldername = $('#folder').val(responseText.tempFolder);

                });

            }

        });

        let hidden_img = $('#hidden_img').val();

        console.log("hidden_img", hidden_id);

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
                    $(".dz-preview").hide();
                    $(".dz-message").show();
                    $("#hidden_img").val("");
                    $("#folder").val("");

                },

                error: function(e) {
                    console.log("error", e);
                }
            })


        })


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
                    let dropzoneWithData = data.dropzoneWithData
                    let dropzoneWithimgArray = data.imgArray

                    console.log("dropzoneWithimgArray", dropzoneWithimgArray);

                    $('#hidden_img').val(dropzoneWithimgArray);
                    $('#dropzoneImg').append(dropzoneWithData);
                    $('#name').val(singleLibraryData.name);
                    $('#hidden_id').val(singleLibraryData.id);
                    $('#isbn').val(singleLibraryData.isbn);
                },
                error: function(e) {
                    console.log("error", e);
                }

            })
        })



        $(document).on('click', '.delete_img', function() {
            let imageName = this.getAttribute('data-id');

            let delId = this.getAttribute('id');

            let hidden_img_val = $('#hidden_img').val();

            var newImageArr = hidden_img_val.split(",");

            // sol1
            //    let imgFinalArr = newImageArr.filter(function(item) {
            //         return item !== imageName;
            //     })

            // sol2
            // let imgFinalArr = newImageArr.findIndex(imageName);

            // sol3 worked
            let arraycontainsImgIndex = (newImageArr.indexOf(imageName));

            newImageArr.splice(arraycontainsImgIndex, 1)

            $('#hidden_img').val(newImageArr);


            $(this).parent().hide();

            $.ajax({
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    'deleteDropzoneImageId': delId,
                    'deleteDropzoneImageName': imageName
                },
                url: "{{ route('dropzone.delete') }}",
                success: function(data) {
                    $('#output').text(data.res);
                    table.ajax.reload();
                },
                error: function(e) {
                    console.log("error", e);
                }

            })
        })



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
