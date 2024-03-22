<?php

namespace App\Console\Commands;

use App\Models\Block;
use App\Models\Transaction;
use Illuminate\Console\Command;

class VerifyEntireBlockChain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:verify-entire-block-chain';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // DB::transaction(function () use ($response) {
            $blocks = Block::all();
            foreach ($blocks as $block) {
                // $blockModel = Block::create([
                //     'id' => $block['id'],
                //     'previous_block_hash' => $block['previous_block_hash'],
                // ]);
                foreach ($block['transactions'] as $transaction) {
                    if (!Transaction::verify((object) $transaction)) {
                        throw new \Exception("transaction with id {$transaction['id']} is invalid");
                    }
                }

                // $blockModel->Transactions()->createMany($block['transactions']);
            }
        // });

        // $block = Block::create([
        //     'id' => request()->block['id'],
        //     'previous_block_hash' => $actualPreviousBlockHash
        // ]);

        // foreach (request()->block['transactions'] as $transaction) {
        //     Transaction::create([
        //         "uploaded_file_path" => $transaction['uploaded_file_path'],
        //         "file_uploaded_by" => $transaction['file_uploaded_by'],
        //         "file_stored_by" => $transaction['file_stored_by'],
        //         "file_hash" => $transaction['file_hash'],
        //         "digital_signature" => $transaction['digital_signature'],
        //         "block_id" => $block->id
        //     ]);
        // }



        // return response()->json([
        //     'message' => 'block is added successfully to the chain',
        // ], 200);
        $this->info('blockchain verified successfully');
    
    }
}
