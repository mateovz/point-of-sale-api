<?php

namespace App\Http\Requests\Product;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class UpdateRequest extends FormRequest
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
            'category_id'   => ['numeric', 'exists:categories,id'],
            'provider_id'   => ['numeric', 'exists:providers,id'],
            'name'          => ['string', 'max:255', 'unique:products,name,'.$this->product],
            'stock'         => ['numeric', 'min:1'],
            'price'         => ['numeric', 'min:0.01'],
            'status'        => ['boolean'],
            'code'          => ['alpha_dash', 'max:255', 'unique:products,code,'.$this->product]
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
