<?php

namespace App\Actions\Invoice;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
use App\Models\Product;

class InvoiceAction
{
    /**
     * Create a new class instance.
     */
    public function create($invoiceData)
    {
        try {
            DB::beginTransaction();

            $invoices = [];
            $productIds = $invoiceData['product_ids'];

            // Create one invoice for each product
            foreach ($productIds as $productId) {
                $product = Product::find($productId);

                if ($product) {
                    $invoice = Invoice::create([
                        'product_id' => $productId,
                        'tripay_reference' => $invoiceData['tripay_reference'],
                        'buyer_email' => $invoiceData['buyer_email'],
                        'buyer_phone' => $invoiceData['buyer_phone'],
                        'raw_response' => $invoiceData['raw_response'],
                    ]);

                    $invoices[] = $invoice;
                }
            }

            DB::commit();
            return $invoices;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
