<?php

namespace App\Helpers;

/**
 * Helper para sanitizar HTML permitindo apenas tags seguras
 * 
 * Este helper remove tags e atributos perigosos, mantendo apenas
 * HTML seguro para exibição (links, formatação básica, etc.)
 */
class HtmlSanitizer
{
    /**
     * Lista de tags HTML permitidas
     */
    private static array $allowedTags = [
        'a', 'span', 'strong', 'em', 'b', 'i', 'u', 'br', 'p', 'div'
    ];
    
    /**
     * Lista de atributos permitidos por tag
     */
    private static array $allowedAttributes = [
        'a' => ['href', 'style', 'class', 'title'],
        'span' => ['style', 'class'],
        'div' => ['style', 'class'],
        'p' => ['style', 'class'],
    ];
    
    /**
     * Sanitiza HTML removendo tags e atributos perigosos
     * 
     * @param string $html HTML a ser sanitizado
     * @return string HTML sanitizado e seguro
     */
    public static function sanitize(string $html): string
    {
        // Se estiver vazio, retornar como está
        if (trim($html) === '') {
            return $html;
        }
        
        // Remover scripts, iframes, objetos e outros elementos perigosos
        $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $html);
        $html = preg_replace('/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/mi', '', $html);
        $html = preg_replace('/<object\b[^<]*(?:(?!<\/object>)<[^<]*)*<\/object>/mi', '', $html);
        $html = preg_replace('/<embed\b[^<]*(?:(?!<\/embed>)<[^<]*)*<\/embed>/mi', '', $html);
        $html = preg_replace('/on\w+="[^"]*"/i', '', $html); // Remove event handlers (onclick, onerror, etc.)
        $html = preg_replace('/on\w+=\'[^\']*\'/i', '', $html);
        $html = preg_replace('/javascript:/i', '', $html); // Remove javascript: em URLs
        
        // Usar strip_tags com tags permitidas
        $allowedTagsString = '<' . implode('><', self::$allowedTags) . '>';
        $html = strip_tags($html, $allowedTagsString);
        
        // Validar e limpar atributos das tags permitidas
        $html = self::cleanAttributes($html);
        
        return $html;
    }
    
    /**
     * Limpa atributos das tags, mantendo apenas os permitidos
     */
    private static function cleanAttributes(string $html): string
    {
        // Para cada tag permitida, validar atributos
        foreach (self::$allowedTags as $tag) {
            $allowedAttrs = self::$allowedAttributes[$tag] ?? [];
            
            // Pattern para encontrar a tag com seus atributos
            $pattern = '/<' . preg_quote($tag, '/') . '(\s[^>]*)?>/i';
            
            $html = preg_replace_callback($pattern, function($matches) use ($tag, $allowedAttrs) {
                $tagContent = $matches[0];
                $attributes = $matches[1] ?? '';
                
                // Extrair atributos permitidos
                $cleanAttrs = [];
                foreach ($allowedAttrs as $attr) {
                    if (preg_match('/\b' . preg_quote($attr, '/') . '\s*=\s*["\']([^"\']*)["\']/i', $attributes, $attrMatch)) {
                        $value = htmlspecialchars($attrMatch[1], ENT_QUOTES, 'UTF-8');
                        // Validar URL em href para prevenir javascript: e data:
                        if ($attr === 'href') {
                            if (!preg_match('/^(https?:\/\/|\/|#)/i', $value)) {
                                continue; // Ignorar hrefs não seguros
                            }
                        }
                        $cleanAttrs[] = $attr . '="' . $value . '"';
                    }
                }
                
                $attrsString = !empty($cleanAttrs) ? ' ' . implode(' ', $cleanAttrs) : '';
                return '<' . $tag . $attrsString . '>';
            }, $html);
        }
        
        return $html;
    }
    
    /**
     * Verifica se uma string contém HTML
     */
    public static function containsHtml(string $string): bool
    {
        return $string !== strip_tags($string);
    }
}

