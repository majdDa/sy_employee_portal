<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMyPasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust this if needed.
    }

    public function rules()
    {
        return [
            'password' => 'required|string|min:6',
        ];
    }
}
