<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AtualizacaoUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'nome' => ['required', 'string', 'min:3', 'max:60', 'regex:/^[A-Za-z\s]+$/'],
            'usuario' => ['required', 'string', 'min:3', 'max:30', 'regex:/^[a-z0-9_]+$/', 'unique:users,usuario,' . $id],
            'email' => ['required', 'email', 'min:10', 'max:35', 'unique:users,email,' . $id],
            'biografia' => ['nullable', 'string', 'max:150'],
            'foto' => ['nullable', 'string'],
            'senha' => ['nullable', 'string', 'min:8', 'max:24', 'regex:/^[A-Za-z0-9]+$/'],
        ];
    }
}