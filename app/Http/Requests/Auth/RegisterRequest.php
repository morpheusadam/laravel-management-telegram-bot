<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string|max:99',
	        'email' => 'required|string|email|max:99|unique:users',
	        'password' => ['required',Rules\Password::defaults()],
	        'terms' => 'accepted'
        ];
    }

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages()
	{
		return [
			'name.required' => __('Name is required'),
			'name.*' => __('Name must be string and must not larger that 99 characters'),
			'email.required' => __('Please enter a valid email'),
			'email.unique' => __('Email is already used'),
			'email.*' => __('Email must be string and must not larger that 99 characters'),
			'password.*' => __('Password is required and minimum 8 characters in length')
		];
	}
}


