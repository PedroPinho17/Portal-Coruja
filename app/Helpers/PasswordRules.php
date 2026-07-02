<?php

namespace App\Helpers;

/**
 * Helper para regras de validação de password
 * 
 * Centraliza as regras de validação de password para garantir consistência
 * em toda a aplicação.
 */
class PasswordRules
{
    /**
     * Regras para password simples (mínimo 8 caracteres)
     * Usado na criação inicial quando mudanca_password=1
     * 
     * @return array
     */
    public static function simple(): array
    {
        return [
            'required',
            'string',
            'min:8',
            'confirmed',
        ];
    }

    /**
     * Regras para password complexa
     * Usado em mudanças de password após o primeiro login
     * 
     * Requisitos:
     * - Mínimo 8 caracteres
     * - Pelo menos uma letra minúscula
     * - Pelo menos uma letra maiúscula
     * - Pelo menos um número
     * - Pelo menos um caractere especial (@$!%*#?&)
     * 
     * @return array
     */
    public static function complex(): array
    {
        return [
            'required',
            'string',
            'min:8',
            'confirmed',
            'regex:/[a-z]/',      // pelo menos uma letra minúscula
            'regex:/[A-Z]/',      // pelo menos uma letra maiúscula
            'regex:/[0-9]/',      // pelo menos um número
            'regex:/[@$!%*#?&]/', // pelo menos um caractere especial
        ];
    }

    /**
     * Regras para password complexa (nullable)
     * Usado quando a password é opcional (ex: atualização de perfil)
     * 
     * @return array
     */
    public static function complexNullable(): array
    {
        return [
            'nullable',
            'string',
            'min:8',
            'confirmed',
            'regex:/[a-z]/',      // pelo menos uma letra minúscula
            'regex:/[A-Z]/',      // pelo menos uma letra maiúscula
            'regex:/[0-9]/',      // pelo menos um número
            'regex:/[@$!%*#?&]/', // pelo menos um caractere especial
        ];
    }

    /**
     * Mensagens de validação personalizadas para password complexa
     * 
     * @return array
     */
    public static function messages(): array
    {
        return [
            'password.regex' => 'A password deve conter pelo menos uma letra minúscula, uma maiúscula, um número e um caractere especial (@$!%*#?&).',
            'password.min' => 'A password deve ter pelo menos 8 caracteres.',
            'password.confirmed' => 'A confirmação da password não corresponde.',
        ];
    }
}

