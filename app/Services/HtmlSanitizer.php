<?php

namespace App\Services;

class HtmlSanitizer
{
    private const ALLOWED_HREF_SCHEMES = ['http', 'https', 'mailto', 'tel'];

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
        $html = preg_replace('/\son\w+\s*=\s*["\'][^"\']*["\']/i', '', $html);
        $html = preg_replace('/\son\w+\s*=\s*[^\s>]+/i', '', $html);

        return preg_replace_callback('/<([a-zA-Z]+)([^>]*)>/', function ($matches) {
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
                if (! in_array($attrName, $allowed)) {
                    continue;
                }

                $value = htmlspecialchars($attr[2], ENT_QUOTES, 'UTF-8');

                if (in_array($attrName, ['href', 'src'])) {
                    if (! $this->isSafeUrl($attr[2])) {
                        $value = $attrName === 'href' ? '#' : '';
                    }
                }

                if ($tagName === 'a' && $attrName === 'rel') {
                    $relTokens = array_filter(explode(' ', $attr[2]));
                    $relTokens[] = 'noopener';
                    $relTokens[] = 'noreferrer';
                    $value = implode(' ', array_values(array_unique($relTokens)));
                }

                $cleanAttributes .= ' '.$attrName.'="'.$value.'"';
            }

            if ($tagName === 'a') {
                $hasTargetBlank = (bool) preg_match('/target\s*=\s*["\']_blank["\']/i', $attributes);
                $hasRel = (bool) preg_match('/\brel\s*=\s*["\']/i', $attributes);

                if ($hasTargetBlank && ! $hasRel) {
                    $cleanAttributes .= ' rel="noopener noreferrer"';
                }
            }

            return '<'.$tagName.$cleanAttributes.'>';
        }, $html);
    }

    private function isSafeUrl(string $url): bool
    {
        $decoded = html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $compact = preg_replace('/[\x00-\x20\x7F]+/', '', $decoded);

        if ($compact === '' || $compact[0] === '/' || $compact[0] === '#' || $compact[0] === '.') {
            return true;
        }

        if (! preg_match('/^([a-z][a-z0-9+.-]*):/i', $compact, $matches)) {
            return true;
        }

        return in_array(strtolower($matches[1]), self::ALLOWED_HREF_SCHEMES);
    }

    private function removeEmptyTags(string $html): string
    {
        $html = preg_replace('/<(\w+)>\s*<\/\1>/', '', $html);

        return $html;
    }
}
