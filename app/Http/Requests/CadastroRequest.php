<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CadastroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'min:3', 'max:60', 'regex:/^[A-Za-z\s]+$/'],
            'usuario' => ['required', 'string', 'min:3', 'max:30', 'regex:/^[a-z0-9_]+$/', 'unique:users,usuario'],
            'email' => ['required', 'email', 'min:10', 'max:35', 'unique:users,email'],
            'senha' => ['required', 'string', 'min:8', 'max:24', 'regex:/^[A-Za-z0-9]+$/'],
            'biografia' => ['nullable', 'string', 'max:150'],
            'foto' => ['nullable', 'string'],
        ];
    }
}