<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'telefon' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'ad_soyad' => 'nullable|string|max:255',
            'fraud_type_id' => 'nullable|exists:fraud_types,id',
            'loss_range_id' => 'nullable|exists:loss_ranges,id',
            'wallet_type_id' => 'nullable|exists:wallet_types,id',
            'status_id' => 'required|exists:lead_statuses,id',
            'atanan_operator_id' => 'nullable|exists:users,id',
        ];
    }
}
