<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Transaction_item;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Str;

class TransactionController extends Controller
{
    public function getTransactionList() 
    {
        $trans = Auth::user()->transactions()->with('transaction_items')->paginate(5);
        if(!$trans) {
            return response()->json([
                'status' => 'error',
                'message' => 'You no have transactions!' 
            ], Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'status' => 'success',
            'transactions' => $trans 
        ], Response::HTTP_OK);
    }

    public function getTransactionDetail($id) 
    {
        $tran = Auth::user()->transactions()->with('transaction_items')->find($id);
        if(!$tran) {
            return response()->json([
                'status' => 'error',
                'message' => 'You no have transaction with the id requested!' 
            ], Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'status' => 'success',
            'transaction' => $tran 
        ], Response::HTTP_OK);
    }

    public function createTransaction(Request $request) 
    {
        
        $data_trans = $request->validate([
            'total_amount' => 'required|integer',
            'paid_amount' => 'required|integer',
            'change_amount' => 'required|integer',
            'payment_method' => 'required|in:cash,card'
        ]);

        $data_tran_items = $request->validate([
            'items.*.title' => 'required|string|max:64',
            'items.*.qty' => 'required|integer',
            'items.*.price' => 'required|integer'
        ]);

        $uuid = Str::uuid()->toString();

        $tran = Transaction::create([
            'uuid' => $uuid,
            'user_id' => Auth::user()->id,
            'device_timestamp' => Carbon::now()->toDateTimeString(),
            'total_amount' => $data_trans['total_amount'],
            'paid_amount' => $data_trans['paid_amount'],
            'change_amount' => $data_trans['change_amount'],
            'payment_method' => $data_trans['payment_method']
        ]);

        foreach ($data_tran_items['items'] as $dt_item) {
            # code...
            $uuid = Str::uuid()->toString();
            Transaction_item::create([
                'uuid' => $uuid,
                'transaction_id' => $tran->id,
                'title' => $dt_item['title'],
                'qty' => $dt_item['qty'],
                'price' => $dt_item['price']
            ]);
        
        }
        
        return response()->json([
            'status' => 'success',
            'message' => "Transaction created successfully!" 
        ], Response::HTTP_OK);
    }

    public function updateTransaction(Request $request, $id) 
    {
        
        $data_trans = $request->validate([
            'total_amount' => 'required|integer',
            'paid_amount' => 'required|integer',
            'change_amount' => 'required|integer',
            'payment_method' => 'required|in:cash,card'
        ]);

        $data_tran_items = $request->validate([
            'items.*.title' => 'required|string|max:64',
            'items.*.qty' => 'required|integer',
            'items.*.price' => 'required|integer'
        ]);

        
        $tran = Auth::user()->transactions()->with('transaction_items')->find($id);
        if(!$tran) {
            return response()->json([
                'status' => 'error',
                'message' => 'You no have transaction with the id requested!' 
            ], Response::HTTP_NOT_FOUND);
        }
        $tran->total_amount = $data_trans['total_amount'];
        $tran->paid_amount = $data_trans['paid_amount'];
        $tran->change_amount = $data_trans['change_amount'];
        $tran->payment_method = $data_trans['payment_method'];
        $tran->save();
        foreach ($tran->transaction_items as $i => $item) {
            $item->title = $data_tran_items['items'][$i]['title'];
            $item->qty = $data_tran_items['items'][$i]['qty'];
            $item->price = $data_tran_items['items'][$i]['price'];
            $item->save();
        }
        

        return response()->json([
            'status' => 'success',
            'message' => "Transaction updated successfully!" 
        ], Response::HTTP_OK);
    }

    public function deleteTransaction($id) 
    {
        
        $tran = Auth::user()->transactions()->with('transaction_items')->find($id);
        if(!$tran) {
            return response()->json([
                'status' => 'error',
                'message' => 'You no have transaction with the id requested!' 
            ], Response::HTTP_NOT_FOUND);
        }
        foreach ($tran->transaction_items as $item) {
            $item->delete();
        }
        $tran->delete();

        return response()->json([
            'status' => 'success',
            'message' => "Transaction deleted successfully!" 
        ], Response::HTTP_OK);
    }


}
