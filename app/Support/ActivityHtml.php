<?php

namespace App\Support;

class ActivityHtml
{
    public static function clean(?string $html): string
    {
        $document = new \DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="utf-8" ?><div>'.($html ?? '').'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        $render = function ($node) use (&$render): string {
            if ($node instanceof \DOMText) {
                return htmlspecialchars($node->textContent, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            }
            if (! $node instanceof \DOMElement) {
                return '';
            }
            $tag = strtolower($node->tagName);
            if (in_array($tag, ['script', 'style', 'iframe', 'object', 'svg', 'math', 'template'])) {
                return '';
            }
            $content = '';
            foreach ($node->childNodes as $child) {
                $content .= $render($child);
            }
            if (! in_array($tag, ['p', 'br', 'h2', 'h3', 'strong', 'em', 'u', 's', 'blockquote', 'ol', 'ul', 'li', 'a'])) {
                return $content;
            }
            if ($tag === 'br') {
                return '<br>';
            }
            $attributes = '';
            if ($tag === 'a') {
                $href = $node->getAttribute('href');
                if (preg_match('~^(https?://|mailto:|/[^/]|#)~i', $href) && ! preg_match('/[\x00-\x20\\\\]/', $href)) {
                    $attributes = ' href="'.htmlspecialchars($href, ENT_QUOTES, 'UTF-8').'" rel="noopener noreferrer"';
                }
            }

            return '<'.$tag.$attributes.'>'.$content.'</'.$tag.'>';
        };
        $result = '';
        foreach ($document->childNodes as $node) {
            $result .= $render($node);
        }

        return $result;
    }
}
