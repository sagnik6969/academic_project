<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

    public function addBlockToBlockChain()
    {
        //$receivedPreviousBlockHash = request()->block->previous_block_hash;
        //$actualPreviousBlockHash = Hash::make(Block::with('transactions')->latest()->first());

        // if ($receivedPreviousBlockHash != $actualPreviousBlockHash) {
        //     return response()->json([
        //         'message' => 'invalid previous block hash'
        //     ], 400);
        // }

        foreach (request()->block['transactions'] as $transaction) {
            if (!Transaction::verify((object) $transaction)) {
                return response()->json([
                    'message' => "transaction with id {$transaction['id']} is invalid"
                ], 400);
            }
        }

        return response()->json([
            'message' => 'block is valid',
        ], 400);

    }


}
