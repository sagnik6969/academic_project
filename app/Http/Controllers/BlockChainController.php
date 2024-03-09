<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\Transaction;
use Illuminate\Http\Request;

class BlockChainController extends Controller
{
    public function getFullBlockChain()
    {
        return Block::with('transactions')->get();
    }

    public function verifyTransaction()
    {
        if (Transaction::verify(request())) {

            return response()->json([
                'message' => 'success'
            ]);

        } else {
            return response()->json([
                'message' => 'failure'
            ], 400);
        }

    }


}
