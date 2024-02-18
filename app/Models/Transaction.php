<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use ParagonIE\Halite\Asymmetric\Crypto;
use ParagonIE\Halite\KeyFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function Block(): BelongsTo
    {
        return $this->belongsTo(Block::class);
    }

    public static function createTransaction($params)
    {
        $privateKey = KeyFactory::loadSignatureSecretKey(base_path() . '/.block_chain_keys/private');
        $publicKey = KeyFactory::loadSignaturePublicKey(base_path() . '/.block_chain_keys/public');
        $signature = Crypto::sign(json_encode($params), $privateKey);

        // if (Crypto::verify(json_encode($params), $publicKey, $signature)) {
        //     Log::info('Signature verified successfully');
        // } else {
        //     Log::info(json_encode($params));
        //     Log::info($publicKey->getRawKeyMaterial());
        //     Log::info($signature);
        //     Log::info('unable to verify signature');
        // }
        // $params['digital_signature'] = $signature;


        $noOfTransactionsInPreviousBlock =
            Block::latest()
                ->first()
                    ?->transactions()
                ->count();



        if ($noOfTransactionsInPreviousBlock && $noOfTransactionsInPreviousBlock < 2) {
            return Block::latest()
                ->first()
                ->transactions()
                ->create($params);
        } else {
            $block = Block::with('transactions')->latest()->first();


            $previousBlockHash = $block ? Hash::make(json_encode($block->toJson())) : '';


            return Block::create([
                'previous_block_hash' => $previousBlockHash
            ])
                ->transactions()
                ->create($params);
        }

    }

}
