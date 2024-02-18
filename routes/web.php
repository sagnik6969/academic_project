<?php

use App\Http\Controllers\FileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;


// function DivideIntoChunks($file,$buffer=1024){
//     //open file to read
//     $file_handle = fopen($file,'r');
//     //get file size
//     $file_size = filesize($file);
//     //no of parts to split
//     $parts = $file_size / $buffer;

//     //store all the file names
//     $file_parts = array();

//     //path to write the final files
//     $store_path = "splits/";

//     //name of input file
//     $file_name = basename($file);

//     for($i=0;$i<$parts;$i++){
//         //read buffer sized amount from file
//         $file_part = fread($file_handle, $buffer);
//         //the filename of the part
//         $file_part_path = $store_path.$file_name.".part$i";
//         //open the new file [create it] to write
//         $file_new = fopen($file_part_path,'w+');
//         //write the part of file
//         // Storage::disk('public')
//         fwrite($file_new, $file_part);
//         //add the name of the file to part list [optional]
//         array_push($file_parts, $file_part_path);
//         //close the part file handle
//         fclose($file_new);
//     }    
//     //close the main file handle

//     fclose($file_handle);
//     return $file_parts;
// }

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


// Route::post('/', [FileController::class, 'store']);
