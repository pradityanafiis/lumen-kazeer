<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Transaction;
use App\ProductTransaction;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class TransactionController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index($range)
    {
        $now = \Carbon\Carbon::now();
        $today = $now->day;
        $thisMonth = $now->month;
        $thisWeek = [$now->startOfWeek()->format('Y-m-d'), $now->endOfWeek()->format('Y-m-d')];
        
        if ($range == 'all') {
            $transactions = Transaction::with('customer', 'product')->orderBy('created_at', 'desc')->get();
        } elseif ($range == 'today') {
            $transactions = Transaction::with('customer', 'product')->whereDay('created_at', $today)->orderBy('created_at', 'desc')->get();
        } elseif ($range == 'this_week') {
            $transactions = Transaction::with('customer', 'product')->whereBetween('created_at', $thisWeek)->orderBy('created_at', 'desc')->get();
        } elseif ($range == 'this_month') {
            $transactions = Transaction::with('customer', 'product')->whereMonth('created_at', $thisMonth)->orderBy('created_at', 'desc')->get();
        }
        
        if ($transactions->isEmpty()) {
            return response()->json([
                'error' => true,
                'message' => 'GetTransaction: FAILED, transaction empty',
                'data' => null
            ]);
        } else {
            $data = collect([]);
            foreach ($transactions as $transaction) {
                $products = ProductTransaction::where('transaction_id', $transaction->id)->get();
                $productsData = collect([]);
                foreach ($products as $product) {
                    $productTemp = [
                        'id' => $product->id,
                        'transaction_id' => $product->transaction_id,
                        'product_id' => $product->product_id,
                        'product_name' => $product->product_name,
                        'product_price' => $product->product_price,
                        'quantity' => $product->quantity,
                        'total_price' => $product->total_price
                    ];
                    $productsData->push($productTemp);
                }
                $temp = [
                    'id' => $transaction->id,
                    'customer_id' => $transaction->customer_id,
                    'invoice_number' => $transaction->invoice_number,
                    'amount' => $transaction->amount,
                    'pay' => $transaction->pay,
                    'change' => $transaction->change,
                    'created_at' => \Carbon\Carbon::parse($transaction->created_at)->format('l, d-m-Y'),
                    'customer' => $transaction->customer,
                    'products' => $productsData
                ];
                $data->push($temp);
            }
            return response()->json([
                'error' => false,
                'message' => 'GetTransaction: SUCCESS',
                'data' => $data
            ]);
        }
    }

    public function getTransactionSummary()
    {
        $transactions = Transaction::selectRaw('DATE(created_at) date, SUM(amount) amount')->groupBy('date')->orderBy('date', 'desc')->limit(7)->get();
        $transactionsData = collect([]);
        foreach ($transactions as $transaction) {
            $temp = [
                'date' => \Carbon\Carbon::parse($transaction->date)->format('j'),
                'amount' => $transaction->amount
            ];
            $transactionsData->push($temp);
        }

        $thisMonth = \Carbon\Carbon::now()->month;
        $countTransaction = Transaction::whereMonth('created_at', $thisMonth)->count();
        $sumAmount = Transaction::whereMonth('created_at', $thisMonth)->sum('amount');

        $data = [
            'count_transaction' => $countTransaction,
            'sum_amount' => $sumAmount,
            'last_week_transaction' => $transactionsData
        ];

        return response()->json([
            'error' => false,
            'message' => 'GetTransactionSummary: SUCCESS',
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => ['required', 'numeric'],
            'amount' => ['required', 'numeric'],
            'pay' => ['required', 'numeric'],
            'change' => ['required', 'numeric']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'CreateTransaction: FAILED',
                'errors' => $validator->errors(),
                'data' => null
            ]);
        } else {
            $id = IdGenerator::generate([
                'table' => 'transaction',
                'field' => 'invoice_number',
                'length' => 6,
                'prefix' => date('ym'),
                'reset_on_prefix_change' => true
            ]);

            $transaction = Transaction::create([
                'customer_id' => $request->customer_id,
                'invoice_number' => $id,
                'amount' => $request->amount,
                'pay' => $request->pay,
                'change' => $request->change
            ]);

            // foreach ($request->products as $product) {
            //     $transaction->product->attach($product['product_id'], [
            //         'product_name' => $product['product_name'],
            //         'product_price' => $product['product_price'],
            //         'quantity' => $product['quantity'],
            //         'total_price' => $product['total_price']
            //     ]);
            // }

            foreach ($request->products as $product) {
                ProductTransaction::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product['product_id'],
                    'product_name' => $product['product_name'],
                    'product_price' => $product['product_price'],
                    'quantity' => $product['quantity'],
                    'total_price' => $product['total_price']
                ]);
            }

            return response()->json([
                'error' => false,
                'message' => 'CreateTransaction: SUCCESS',
                'data' => $transaction
            ]);
        }
    }
}