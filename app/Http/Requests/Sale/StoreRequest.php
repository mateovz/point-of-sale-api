<?php

namespace App\Http\Requests\Sale;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'client_id'     => ['required', 'numeric', 'exists:clients,id'],
            'user_id'       => ['required', 'numeric', 'exists:users,id'],
            'sale_date'     => ['date', 'before:tomorrow'],
            'tax'           => ['numeric', 'max:100'],
            'status'        => ['boolean'],

            // sale details
            'products'                  => ['required', 'array'],
            'products.*.product_id'     => ['required', 'numeric', 'exists:products,id'],
            'products.*.quantity'       => ['required', 'numeric', 'min:1'],
            'products.*.price'          => ['numeric'],
            'products.*.discount'       => ['numeric', 'min:0', 'max:100']
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(
            response()->json([
                'status'    => 'error',
                'errors'    => $errors
            ], 400)
        );
    }
}
