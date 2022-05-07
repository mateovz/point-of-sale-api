<?php

namespace App\Http\Requests\Provider;

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
            'name'      => ['required', 'string', 'max:100', 'unique:providers,name,'.$this->provider],
            'email'     => ['required', 'email', 'max:255', 'unique:providers,email,'.$this->provider],
            'address'   => ['nullable', 'string', 'max:255'],
            'phone'     => ['nullable', 'string', 'max:20'],
            'ruc'       => ['nullable', 'numeric', 'digits_between:1,50', 'unique:providers,ruc,'.$this->provider]
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
