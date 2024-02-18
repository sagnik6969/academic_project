<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ParagonIE\Halite\KeyFactory;
use Spatie\Crypto\Rsa\KeyPair;

class GenerateEncryptionKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-encryption-keys';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates private and public keys for authentication in blockchain';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sign_keyPair = KeyFactory::generateSignatureKeyPair();
        $sign_secret = $sign_keyPair->getSecretKey();
        $sign_public = $sign_keyPair->getPublicKey();

        KeyFactory::save($sign_secret, base_path() . '/.block_chain_keys/private');
        KeyFactory::save($sign_public, base_path() . '/.block_chain_keys/public');

        $this->info('blockchain keys generated successfully!!');

        // https://github.com/paragonie/halite/blob/master/doc/Basic.md

    }
}
