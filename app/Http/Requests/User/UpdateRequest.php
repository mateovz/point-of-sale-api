<?php

namespace App\Http\Requests\User;

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
            'name'  => ['string'],
            'email' => ['email', 'unique:users,email,'.$this->user],

            'roles'             => ['array'],
            'roles.add'         => ['array'],
            'roles.add.*.id'    => ['required', 'numeric', 'exists:roles,id'],   
            'roles.remove'      => ['array'],
            'roles.remove.*.id' => ['required', 'numeric'],

            'avatar'    => ['image', 'mimes:png,jpg,jpeg', 'max:2048', 'dimensions:max_width=1000,max_height=1000']
        ];
    }

    protected function prepareForValidation()
    {
        if(isset($this->userData) && $this->userData){
            $this->merge(json_decode($this->userData, true, 512, JSON_THROW_ON_ERROR));
        }

        if(isset($this->avatar) && $this->avatar){
            $this->merge(['avatar' => $this->avatar]);
        }
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
