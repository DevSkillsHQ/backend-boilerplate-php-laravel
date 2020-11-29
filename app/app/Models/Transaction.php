<?php

namespace App\Models;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Transaction
 *
 * @property string $id
 * @property string $account_id
 * @property int $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Account $account
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Transaction extends Model
{
    use HasFactory;
    use Uuid;

    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'amount' => 'integer'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class)->withDefault();
    }


    /**
     * @param $transaction_id
     * @param $account_id
     * @param $amount
     * @throws \Throwable
     */
    public static function insertOne($transaction_id, $account_id, $amount)
    {
        \DB::transaction(function () use ($transaction_id, $account_id, $amount){

            $account = Account::where('id', $account_id)->first() ?? Account::create(['id' => $account_id]);

            $transaction = Transaction::create([
                'id' => $transaction_id,
                'account_id' => $account->id,
                'amount' => $amount
            ]);

            $transaction->account()->update([
                'balance' => $account->balance + ($transaction->amount)
            ]);

        });
    }

    /**
     * @return array
     */
    public static function findMaxVolumes()
    {
        return \DB::select("
                    SELECT COUNT(id) as max_volume, account_id
                    FROM transactions
                    GROUP BY account_id
                    ORDER BY max_volume DESC;"
        );
    }
}
