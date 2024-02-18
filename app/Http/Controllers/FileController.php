<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class FileController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file'
        ]);

        $file = $request->file('file');
        $path = $file->store('/tests', 'public');
        // return $file->getContent();
        $fileContent = $file->getContent();
        $fileHash = Hash::make($fileContent);

        $transaction = Transaction::createTransaction([
            'uploaded_file_path' => url('storage/' . $path),
            'file_uploaded_by' => 'sagnik',
            'file_stored_by' => env('APP_PUBLIC_KEY'),
            'file_hash' => $fileHash,

        ]);
        $transaction->load('block');
        // return redirect(url('storage/' . $path));
        return $transaction;

    }
}
