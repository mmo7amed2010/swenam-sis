<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadPaymentReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_receipt' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_receipt.required' => 'Please upload a payment receipt.',
            'payment_receipt.mimes' => 'The payment receipt must be a PDF, JPG, or PNG file.',
            'payment_receipt.max' => 'The payment receipt must not exceed 10MB.',
        ];
    }
}
