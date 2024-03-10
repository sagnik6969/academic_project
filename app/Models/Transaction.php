<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
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

        $transaction = Transaction::create([
            ...$params,
            'digital_signature' => $signature

        ]);

        //KeyFactory::importSignaturePublicKey() => to derive the key from string
        // if (Crypto::verify(json_encode($params), $publicKey, $signature)) {
        //     Log::info('Signature verified successfully');
        // } else {
        //     Log::info(json_encode($params));
        //     Log::info($publicKey->getRawKeyMaterial());
        //     Log::info($signature);
        //     Log::info('unable to verify signature');
        // }
        // $params['digital_signature'] = $signature;


        $numberOfTransactionsWhichAreNotAddedToBlock =
            Transaction::whereNull('block_id')->count();


        if ($numberOfTransactionsWhichAreNotAddedToBlock >= 2) {
            $block = Block::with('transactions')->latest()->first();

            $previousBlockHash = $block ? Hash::make(json_encode($block->toJson())) : '';

            $block = Block::create([
                'previous_block_hash' => $previousBlockHash
            ]);

            Transaction::whereNull('block_id')
                ->get()
                ->each(
                    function ($transaction) use ($block) {
                        $transaction->block_id = $block->id;
                        $transaction->save();
                    }
                );

            $block->load('transactions');

            Http::post(env("OTHER_CSP_URL", 'http://127.0.0.1:8080') . '/api/verify_block', [
                'block' => $block
            ]);

        }

        return $transaction;
    }

    public static function verify($transaction)
    {
        try {
            $publicKeyString = $transaction->file_stored_by;
            $publicKeyFile = tempnam(sys_get_temp_dir(), 'temp_');
            file_put_contents($publicKeyFile, $publicKeyString);
            $publicKey = KeyFactory::loadSignaturePublicKey($publicKeyFile);
            unlink($publicKeyFile);

            $signature = $transaction->digital_signature;

            return Crypto::verify(json_encode([
                'uploaded_file_path' => $transaction->uploaded_file_path,
                'file_uploaded_by' => $transaction->file_uploaded_by,
                'file_stored_by' => $transaction->file_stored_by,
                'file_hash' => $transaction->file_hash,
            ]), $publicKey, $signature);

        } catch (\Throwable $th) {
            return false;
        }


    }

}
