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

    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute deve ser um texto.',
            'min' => 'O campo :attribute deve ter no mínimo :min caracteres.',
            'max' => 'O campo :attribute deve ter no máximo :max caracteres.',
            'email' => 'O campo :attribute deve ser um e-mail válido.',
            'unique' => 'O :attribute informado já está em uso.',
            'nome.regex' => 'O nome deve conter apenas letras e espaços.',
            'usuario.regex' => 'O usuário deve conter apenas letras minúsculas, números e underline.',
            'senha.regex' => 'A senha deve conter apenas letras e números.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nome' => 'nome',
            'usuario' => 'usuário',
            'email' => 'e-mail',
            'senha' => 'senha',
            'biografia' => 'biografia',
            'foto' => 'foto',
        ];
    }
}