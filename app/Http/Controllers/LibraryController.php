<?php

namespace App\Http\Controllers;

use App\Models\Imagetable;
use App\Models\Library;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class LibraryController extends Controller
{

    function index()
    {
        return view('index');
    }

    function libraryView(Request $request)
    {
        echo '<pre>';
print_r($request->all());
die;

        $edit_id = $request['editId'];

        $hidden_id = $request['hidden_id'];

        $hidden_img = $request['hidden_img'];


        $delete_id = $request['deleteId'];


        $imgArray = explode(",", $hidden_img);


        if ($request['type'] == 'insert') {

            echo "We are reaching in insert here!";


            $libModel = new Library;

            if ($hidden_id) {
                $libModel = Library::find($hidden_id);
            }

            $libModel->name = $request->name;
            $libModel->isbn = $request->isbn;
            $libModel->save();

            $lastId = DB::getPdo()->lastInsertId();

            $folder = $request['folder'];

           
            $uploadPath = public_path('upload/' . $folder);
            
            // : public_path('upload/' . $folder)

            $newfolderPath = public_path('upload/' . $lastId);
            

            if (File::exists($uploadPath)) {
                rename($uploadPath, $newfolderPath);
            echo "come on file is changing";

            } else {
                echo '<pre>';
                print_r("file not exists!");
                die;
            }


            foreach ($imgArray as $key => $singleImage) {
            echo "Urvish DB sudhu pauchi gaya";

                Imagetable::create([
                    'mainId' => $lastId,
                    'image' => $singleImage
                ]);
            }

            return response()->json(['res' => "data successfully inserted into db"]);

        } else if ($request['type'] == 'edit') {

            $singleLibraryData1 = Library::select('*')->where('id', $edit_id)->get();

            $singleLibraryData = $singleLibraryData1[0];

            $imageTableData = Imagetable::select('image')->where('mainId', $edit_id)->get();

            $imgArray = [];

            $dropzoneWithData = '';

            foreach ($imageTableData as $key => $singleImageTableData) {
                $singleImg = $singleImageTableData->image;
                array_push($imgArray, $singleImg);
            }


            foreach ($imgArray as $key => $perImg) {

                $dropzoneWithData .= "
                    <div class='image'>  
                     <img src='" . asset("upload/" . $edit_id . "/" . $perImg) . "' alt='dropzone image' height='100px' weight='auto'>
                    
                     <button class='btn btn-danger delete_img' data-id='" . $perImg . "' id='" . $edit_id . "'>Delete</button> 
                     </div>
                   ";
            }


            return response()->json(['singleLibraryData' => $singleLibraryData, 'dropzoneWithData' => $dropzoneWithData, 'imgArray' => $imgArray]);

        } elseif ($request['type'] == 'delete') {

            $user = Library::find($delete_id);

            $user->delete();

            // $imageTable = Imagetable::find($delete_id)->where("status", 1);
            $imageTable = Imagetable::where("mainId", $delete_id)->delete();
            // $imageTable->delete();

            $newfolderPath = public_path('upload/' . $delete_id);


            if (File::exists($newfolderPath)) {
                if (File::deleteDirectory($newfolderPath)) {
                    echo '<pre>';
                    print_r("The folder has been deleted");
                    die;
                } else {
                    echo '<pre>';
                    print_r("Issue in the code!");
                    die;
                }
            } 


            return response()->json(['res' => "The Record $delete_id deleted successfully"]);
        }

    }

    public function upload(Request $request)
    {
        // echo "We reached in upload";

        $temp = "tempFolder";

        $tempFolder = time() . '.' . $temp;

        $uploadPath = public_path('upload/' . $tempFolder);

        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0777, true, true);
            // echo "Navu file upload fuc ma bani gayi jo";
        }

        $img = $request->file('file');
        $allimg = [];


        foreach ($img as $key => $singleImg) {
            $imageName = $singleImg->getClientOriginalName();
            $singleImg->move($uploadPath, $imageName);
            array_push($allimg, $imageName);
        }


        $temparr = ["allimg" => $allimg, "tempFolder" => $tempFolder];

     
        return $temparr;
    }


    public function dropzoneDelete(Request $request)
    {

        $deleteDropzoneId = $request->deleteDropzoneImageId;
       

        $deleteDropzoneImageName = $request->deleteDropzoneImageName;

        $uploadPath = public_path('upload' . '/' . $deleteDropzoneId);
    

        if (File::exists($uploadPath)) {
            echo "We reached here!";
            if (File::delete($uploadPath . '/' . $deleteDropzoneImageName)) {
                echo "The file has been deleted successfully!";
            } else {
                echo '<pre>';
                print_r("file not exists!");
                die;
            }
        }

        $imageTable = Imagetable::where("image", $deleteDropzoneImageName)->where("mainId", $deleteDropzoneId)->delete();

        if ($imageTable == 1) {
            echo "The Image has been deleted successfully from database!";
        } else {
            echo "Please check the error and try again!";
        }


    }


    function listing()
    {
        $libraryData = Library::all();

        $tbody = [];

        foreach ($libraryData as $value) {

            $tbody[] = [
                'id' => $value['id'],
                'name' => $value['name'],
                'isbn' => $value['isbn'],
                'image' => '<img src="' . asset('uploads/' . $value['image']) . '" alt="Not Found!" style="max-height: 50px;">',
                'action' => '<button id="' . $value['id'] . '" class="btn btn-warning edit">Edit</button> | <button id="' . $value['id'] . '" class="btn btn-danger delete">Delete</button>',
            ];
        }


        $output = ['data' => $tbody];


        return json_encode($output);
    }
}