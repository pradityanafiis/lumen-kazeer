<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Customer;

class CustomerController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index()
    {
        $customer = Customer::orderBy('name', 'asc')->get();
        if ($customer->isEmpty()) {
            return response()->json([
                'error' => true,
                'message' => 'GetCustomer: FAILED, customer empty',
                'data' => null
            ]);
        }else {
            return response()->json([
                'error' => false,
                'message' => 'GetCustomer: SUCCESS',
                'data' => $customer
            ]);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
            'address' => ['required', 'string'],
            'telephone' => ['required', 'numeric', 'digits_between:10,13']
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'CreateCustomer: FAILED',
                'errors' => $validator->errors(),
                'data' => null
            ]);
        }else {
            $customer = Customer::create([
                'name' => $request->name,
                'address' => $request->address,
                'telephone' => $request->telephone
            ]);

            return response()->json([
                'error' => false,
                'message' => 'CreateCustomer: SUCCESS',
                'data' => $customer
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
            'address' => ['required', 'string'],
            'telephone' => ['required', 'numeric', 'digits_between:10,13']
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'EditCustomer: FAILED',
                'errors' => $validator->errors(),
                'data' => null
            ]);
        }else {
            $customer = Customer::find($id);
            $customer->update([
                'name' => $request->name,
                'address' => $request->address,
                'telephone' => $request->telephone
            ]);

            return response()->json([
                'error' => false,
                'message' => 'EditCustomer: SUCCESS',
                'data' => $customer
            ]);
        }
    }

    public function destroy($id)
    {
        $customer = Customer::find($id);
        if ($customer->transaction->isEmpty()) {
            $customer->delete();
            return response()->json([
                'error' => false,
                'message' => 'DeleteCustomer: SUCCESS',
                'data' => $customer
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Failed, please delete all transactions with this customer first!',
                'data' => $customer
            ]);
        }
    }
}
