<?php

namespace App\Http\Requests\Provider;

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
            'name'      => ['required', 'string', 'max:100', 'unique:providers'],
            'email'     => ['required', 'email', 'max:255', 'unique:providers'],
            'address'   => ['nullable', 'string', 'max:255'],
            'phone'     => ['nullable', 'numeric', 'max:20'],
            'ruc'       => ['nullable', 'numeric', 'max:50', 'unique:providers']
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
