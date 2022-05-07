<?php

namespace App\Http\Requests\Client;

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
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'              => ['required', 'string', 'max:150'],
            'identification'    => ['required', 'alpha_dash', 'max:50', 'unique:clients,identification,'.$this->client],
            'ruc'               => ['nullable', 'numeric', 'max:50', 'unique:clients,ruc,'.$this->client],
            'email'             => ['required', 'email', 'max:255', 'unique:clients,email,'.$this->client],
            'address'           => ['nullable', 'string', 'max:255']
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
