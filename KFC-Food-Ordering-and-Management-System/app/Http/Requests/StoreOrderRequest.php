<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'items'               => ['required','array','min:1'],
            'items.*.food_id'     => ['required','integer','min:1'],
            'items.*.quantity'    => ['required','integer','min:1'],
        ];
    }
}
