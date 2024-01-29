<?php

namespace App\Http\Controllers;

use App\Models\Library;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LibraryController extends Controller
{

    function index()
    {
        return view('index');
    }

    function libraryView(Request $request)
    {
        $edit_id = $request['editId'];

        $hidden_id = $request['hidden_id'];

        $delete_id = $request['deleteId'];


        if ($request['type'] == 'insert') {

            $extension = $request->file('image')->getClientOriginalExtension();

            $imageName = time() . '.' . $extension;

            $uploadDirectory = 'uploads';

            $uploadPath = public_path($uploadDirectory);

            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0777, true, true);
            }

            $request->file('image')->move($uploadPath, $imageName);
            $imagePath = $uploadDirectory . '/' . $imageName;


            $libModel = new Library;

            if ($hidden_id) {
                $libModel = Library::find($hidden_id);
            }

            $libModel->name = $request->name;
            $libModel->isbn = $request->isbn;
            $libModel->image = $imageName;
            $libModel->save();

            return response()->json(['res' => "data successfully inserted into db"]);

        } else if ($request['type'] == 'edit') {

            $singleLibraryData1 = Library::select('*')->where('id', $edit_id)->get();

            $singleLibraryData = $singleLibraryData1[0];

            return response()->json(['singleLibraryData' => $singleLibraryData]);

        } 
        elseif ($request['type'] == 'delete') {
            $user = Library::find($delete_id);

            $user->delete();

            return response()->json(['res' => "The Record $delete_id deleted successfully"]);

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
