<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{

    public function create(Request $request)
    {
        $transaction_id = $request->header('Transaction-Id');
        $account_id = $request->post('account_id');
        $amount = $request->post('amount');

        $data = array_merge($request->all(), ['transaction_id' => $transaction_id]);

        $validator = \Validator::make($data, [
            'transaction_id' => 'required|uuid',
            'account_id' => 'required|uuid',
            'amount' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response('Mandatory body parameters missing or have incorrect type.', 400);
        }

        try {
            Transaction::insertOne($transaction_id, $account_id, $amount);
        } catch (\Exception $exception) {
            return  response('Something went wrong. Message: '. $exception->getMessage());
        }


        return response('Transaction created.');
    }


    public function maxTransactionVolume()
    {
        $results = Transaction::findMaxVolumes();

        $maxVolume = max(array_column($results, 'max_volume'));
        $accounts = array_column(array_filter($results, fn($item) => $item->max_volume == $maxVolume), 'account_id');

        return response([
            'maxVolume' => (int)$maxVolume,
            'accounts' => $accounts
        ]);
    }
}
