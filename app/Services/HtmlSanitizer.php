<?php

namespace App\Services;

class HtmlSanitizer
{
    private array $allowedTags = [
        'p', 'br', 'strong', 'em', 'u', 's', 'h2', 'h3', 'h4', 'h5', 'h6',
        'blockquote', 'ul', 'ol', 'li', 'a', 'img', 'figure', 'figcaption',
        'table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td', 'caption',
        'pre', 'code', 'hr', 'span', 'div', 'sub', 'sup', 'small',
    ];

    private array $allowedAttributes = [
        'a' => ['href', 'title', 'target', 'rel'],
        'img' => ['src', 'alt', 'title', 'width', 'height', 'class'],
        'td' => ['colspan', 'rowspan'],
        'th' => ['colspan', 'rowspan'],
        'span' => ['class'],
        'div' => ['class'],
        'p' => ['class'],
        'figure' => ['class'],
        'figcaption' => ['class'],
        'table' => ['class'],
        'code' => ['class'],
        'pre' => ['class'],
    ];

    public function sanitize(?string $dirty): string
    {
        if (empty($dirty)) {
            return '';
        }

        $dirty = $this->stripDisallowedTags($dirty);
        $dirty = $this->stripDangerousAttributes($dirty);
        $dirty = $this->removeEmptyTags($dirty);

        return $dirty;
    }

    private function stripDisallowedTags(string $html): string
    {
        $tagPattern = '/<(\/?)(\w+)[^>]*>/i';

        return preg_replace_callback($tagPattern, function ($matches) {
            $tagName = strtolower($matches[2]);
            if (in_array($tagName, $this->allowedTags)) {
                return $matches[0];
            }

            return '';
        }, $html);
    }

    private function stripDangerousAttributes(string $html): string
    {
        $html = preg_replace('/\bon\w+\s*=\s*["\'][^"\']*["\']/i', '', $html);
        $html = preg_replace('/\bon\w+\s*=\s*\S+/i', '', $html);
        $html = preg_replace('/href\s*=\s*["\']javascript:[^"\']*["\']/i', 'href="#"', $html);
        $html = preg_replace('/src\s*=\s*["\']javascript:[^"\']*["\']/i', 'src=""', $html);
        $html = preg_replace_callback('/<([a-zA-Z]+)([^>]*)>/', function ($matches) {
            $tagName = strtolower($matches[1]);
            $attributes = $matches[2];

            if (! isset($this->allowedAttributes[$tagName])) {
                return '<'.$tagName.'>';
            }

            $allowed = $this->allowedAttributes[$tagName];
            preg_match_all('/(\w+)\s*=\s*["\']([^"\']*)["\']/i', $attributes, $attrMatches, PREG_SET_ORDER);

            $cleanAttributes = '';
            foreach ($attrMatches as $attr) {
                $attrName = strtolower($attr[1]);
                if (in_array($attrName, $allowed)) {
                    $cleanAttributes .= ' '.$attr[1].'="'.htmlspecialchars($attr[2], ENT_QUOTES, 'UTF-8').'"';
                }
            }

            return '<'.$tagName.$cleanAttributes.'>';
        }, $html);

        return $html;
    }

    private function removeEmptyTags(string $html): string
    {
        $html = preg_replace('/<(\w+)>\s*<\/\1>/', '', $html);

        return $html;
    }
}
