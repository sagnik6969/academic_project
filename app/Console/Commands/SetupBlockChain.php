<?php

namespace App\Console\Commands;

use App\Models\Block;
use App\Models\Transaction;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class SetupBlockChain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup-block-chain';

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
        $this->call('db:wipe');
        $this->call('migrate');
        $response = Http::get(env('OTHER_CSP_URL', 'http://127.0.0.1:8080') . '/api/full_blockchain');

        // $this->info($blocks);
        DB::transaction(function () use ($response) {
            $blocks = json_decode($response->body(), true);
            foreach ($blocks as $block) {
                $blockModel = Block::create([
                    'id' => $block['id'],
                    'previous_block_hash' => $block['previous_block_hash'],
                ]);
                foreach ($block['transactions'] as $transaction) {
                    if (!Transaction::verify((object) $transaction)) {
                        throw new Exception("transaction with id {$transaction['id']} is invalid");
                    }
                }

                $blockModel->Transactions()->createMany($block['transactions']);
            }
        });

    }
}
