<?php
#author’s name： Yew Kai Quan
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FoodIndexRequests extends FormRequest
{
    public function rules(): array
    {
        return [
            'category'   => ['nullable','string'],
            'search'     => ['nullable','string'],
            'min_rating' => ['nullable','numeric','min:0','max:5'],
            'sort'       => ['nullable','in:name,price,rating,reviews'],
            'dir'        => ['nullable','in:asc,desc'],
            'per_page'   => ['nullable','integer','min:1','max:50'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
