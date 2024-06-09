<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers(),
            ],
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'El campo nombre es obligatorio.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password' => [
                'letters' => 'La contraseña debe contener al menos una letra.',
                'mixedCase' => 'La contraseña debe contener letras mayúsculas y minúsculas.',
                'numbers' => 'La contraseña debe contener al menos un número.',
                'min' => 'La contraseña debe tener al menos :min caracteres.',
                'confirmed' => 'La confirmación de la contraseña no coincide.',
            ],
        ];
    }
}
