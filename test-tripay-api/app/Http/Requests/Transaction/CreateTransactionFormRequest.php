<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class CreateTransactionFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'method' => 'required|string',
            'merchant_ref' => 'required|string',
            'amount' => 'required|integer|min:0',
            'customer_name' => 'required|string',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string',
            'callback_url' => 'nullable|url',
            'return_url' => 'nullable|url',
            'order_items' => 'required|array|min:1',
            'order_items.*.id' => 'required|integer|exists:products,id',
            'order_items.*.name' => 'required|string',
            'order_items.*.price' => 'required|integer|min:0',
            'order_items.*.quantity' => 'required|integer|min:1',
            'expired_time' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'order_items.*.id.exists' => 'The selected product does not exist.',
            'order_items.min' => 'At least one order item is required.',
            'amount.min' => 'The amount must be at least 0.',
            'order_items.*.price.min' => 'Each item price must be at least 0.',
            'order_items.*.quantity.min' => 'Each item quantity must be at least 1.',
        ];
    }
}
