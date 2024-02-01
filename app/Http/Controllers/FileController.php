<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class FileController extends Controller
{
    public function store(Request $request)
    {
        $file = $request->file('file');
        $file->store('/tests', 'public');
        // return $file->getContent();
        return Hash::make($file->getContent());

    }
}
