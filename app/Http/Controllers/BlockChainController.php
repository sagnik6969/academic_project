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
        $receivedPreviousBlockHash = request()->block['previous_block_hash'];
        $previousBlockCount = Block::count();
        $actualPreviousBlockHash = $previousBlockCount ? Hash::make(Block::with('transactions')->latest()->first()) : '';

        if ($receivedPreviousBlockHash != $actualPreviousBlockHash) {
            return response()->json([
                'message' => 'invalid previous block hash'
            ], 400);
        }

        foreach (request()->block['transactions'] as $transaction) {
            if (!Transaction::verify((object) $transaction)) {
                return response()->json([
                    'message' => "transaction with id {$transaction['id']} is invalid"
                ], 400);
            }
        }

        $block = Block::create([
            'id' => request()->block['id'],
            'previous_block_hash' => $actualPreviousBlockHash
        ]);

        foreach (request()->block['transactions'] as $transaction) {
            Transaction::create([
                "uploaded_file_path" => $transaction['uploaded_file_path'],
                "file_uploaded_by" => $transaction['file_uploaded_by'],
                "file_stored_by" => $transaction['file_stored_by'],
                "file_hash" => $transaction['file_hash'],
                "digital_signature" => $transaction['digital_signature'],
                "block_id" => $block->id
            ]);
        }



        return response()->json([
            'message' => 'block is added successfully to the chain',
        ], 200);

    }


}
