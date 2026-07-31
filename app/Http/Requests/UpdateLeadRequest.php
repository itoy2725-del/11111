<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'telefon' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'ad_soyad' => 'nullable|string|max:255',
            'fraud_type_id' => 'nullable|exists:fraud_types,id',
            'loss_range_id' => 'nullable|exists:loss_ranges,id',
            'wallet_type_id' => 'nullable|exists:wallet_types,id',
            'status_id' => 'nullable|exists:lead_statuses,id',
            'atanan_operator_id' => 'nullable|exists:users,id',
            'operator_notu' => 'nullable|string',
            'sonraki_arama_tarihi' => 'nullable|date'
        ];
    }
}
