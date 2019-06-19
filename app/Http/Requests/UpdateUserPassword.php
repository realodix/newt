<?php

namespace App\Http\Requests;

use App\Rules\Auth\CurrentPassword;
use App\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserPassword extends FormRequest
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
            'current-password' => [new CurrentPassword],
            'new-password'     => ['required', 'different:current-password', 'string', 'min:6', 'confirmed'],
        ];
    }
}
