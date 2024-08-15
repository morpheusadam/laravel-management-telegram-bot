<?php

namespace App\Http\Requests\Api\User;

use App\Http\Requests\Api\FailedValidation;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    use FailedValidation;

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
            'name' => 'sometimes|min:3|max:50',
            'package_id' => 'sometimes|exists:packages,id',
            'expired_date' => 'sometimes|date',
        ];
    }
}
