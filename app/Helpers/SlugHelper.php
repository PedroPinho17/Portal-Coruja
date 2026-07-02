<?php
namespace App\Helpers;

class SlugHelper
{
    /**
     * Gera um slug seguro para nomes de ficheiros e URLs.
     * Remove acentos, caracteres especiais e converte espaços em hífens.
     */
    public static function slugify($string)
    {
        $string = strtolower($string);
        $string = preg_replace('/[áàãâä]/u', 'a', $string);
        $string = preg_replace('/[éèêë]/u', 'e', $string);
        $string = preg_replace('/[íìîï]/u', 'i', $string);
        $string = preg_replace('/[óòõôö]/u', 'o', $string);
        $string = preg_replace('/[úùûü]/u', 'u', $string);
        $string = preg_replace('/[ç]/u', 'c', $string);
        $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
        $string = preg_replace('/[\s-]+/', '-', $string);
        $string = trim($string, '-');
        return $string;
    }

    /**
     * Gera nome de ficheiro PDF para apresentação.
     * Exemplo: categoria-subcategoria-titulo-idioma.pdf
     */
    public static function pdfFileName($categoria, $subcategoria, $titulo, $idioma)
    {
        $parts = [
            self::slugify($categoria),
            self::slugify($subcategoria),
            self::slugify($titulo),
            self::slugify($idioma)
        ];
        return implode('-', array_filter($parts)) . '.pdf';
    }
}
