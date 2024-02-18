<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
            return Block::create()
                ->transactions()
                ->create($params);
        }

    }

}
