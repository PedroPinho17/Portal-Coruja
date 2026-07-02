<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin Basic Authentication
    |--------------------------------------------------------------------------
    |
    | Configuração para autenticação HTTP Basic do painel administrativo.
    | Estas credenciais são usadas como uma camada adicional de segurança
    | antes do login normal do Laravel.
    |
    | IMPORTANTE: Em produção, sempre defina valores seguros para estas
    | credenciais no arquivo .env
    |
    */

    'basic_auth' => [
        'user' => env('ADMIN_USER'),
        'pass' => env('ADMIN_PASS'),
    ],

];

