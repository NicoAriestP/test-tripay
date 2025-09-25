<?php

namespace App\Http\Controllers;

use App\Http\Requests\Transaction\CreateTransactionFormRequest;
use App\Models\Product;
use App\Actions\Invoice\InvoiceAction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class TransactionController extends Controller
{
    private string $tripayAPISandboxUrl;
    private string $tripayAPIKey;
    private string $tripayPrivateKey;
    private string $tripayMerchantCode;

    public function __construct() {
        $this->tripayAPISandboxUrl = env('TRIPAY_API_SANDBOX_URL');
        $this->tripayAPIKey = env('TRIPAY_API_KEY');
        $this->tripayPrivateKey = env('TRIPAY_PRIVATE_KEY');
        $this->tripayMerchantCode = env('TRIPAY_MERCHANT_CODE');
    }

    public function paymentChannels()
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->tripayAPIKey,
        ])->get($this->tripayAPISandboxUrl . '/merchant/payment-channel');

        $paymentChannels = $response->json();

        if ($response->successful()) {
            return response()->json($paymentChannels, 200);
        } else {
            return response()->json([
                'message' => 'Failed to fetch payment channels',
                'details' => $paymentChannels,
            ], $response->status());
        }
    }

    public function create(CreateTransactionFormRequest $request, InvoiceAction $invoiceAction)
    {
        $payload = [
            'method' => $request->input('method'),
            'merchant_ref' => $request->input('merchant_ref'),
            'amount' => $request->input('amount'),
            'customer_name' => $request->input('customer_name'),
            'customer_email' => $request->input('customer_email'),
            'customer_phone' => $request->input('customer_phone'),
            'order_items' => array_map(fn($item) => Arr::except($item, ['id']), $request->input('order_items')),
            'signature' => hash_hmac('sha256', $this->tripayMerchantCode . $request->input('merchant_ref') . $request->input('amount'), $this->tripayPrivateKey),
        ];

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->tripayAPIKey,
        ])->post($this->tripayAPISandboxUrl . '/transaction/create', $payload);

        if (! $response->successful()) {
            return response()->json([
                'message' => 'Error requesting transaction from tripay',
                'details' => $response->json(),
            ], $response->status());
        }

        $transactionData = $response->json();

        $invoiceData = [
            'product_ids' => array_map(fn($item) => $item['id'], $request->input('order_items')),
            'tripay_reference' => $transactionData['data']['reference'],
            'buyer_email' => $request->input('customer_email'),
            'buyer_phone' => $request->input('customer_phone'),
            'raw_response' => $transactionData,
        ];

        if ($transactionData) {
            $invoiceAction->create($invoiceData);

            return response()->json($transactionData, 200);
        } else {
            return response()->json([
                'message' => 'Failed to create transaction',
            ], $response->status());
        }
    }

}
