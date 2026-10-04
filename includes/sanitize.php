<?php
/**
 * Allowlist HTML sanitiser for content staff enter in the admin (headings, rich text,
 * social icons, livestream notes). Anything not on the allowlist is removed:
 * scripts, event handlers (onclick, onerror…), javascript: links, iframes, forms, styles.
 *
 * Profiles:
 *  - 'basic'  formatting and links (page titles, paragraphs, long descriptions)
 *  - 'notes'  basic + fill-in-the-blank inputs used by livestream notes and prompts
 *  - 'svg'    inline SVG icons only (social media icons)
 */

if (!function_exists('safe_html')) {
    function safe_html_profiles(): array
    {
        $text = ['class', 'title', 'id', 'aria-label', 'aria-hidden', 'role', 'lang', 'dir'];
        $basic = [
            'p' => $text, 'br' => [], 'hr' => $text, 'span' => $text, 'div' => $text,
            'strong' => $text, 'b' => $text, 'em' => $text, 'i' => $text, 'u' => $text, 's' => $text,
            'small' => $text, 'sup' => $text, 'sub' => $text, 'mark' => $text,
            'h1' => $text, 'h2' => $text, 'h3' => $text, 'h4' => $text, 'h5' => $text, 'h6' => $text,
            'ul' => $text, 'ol' => $text, 'li' => $text, 'blockquote' => $text, 'cite' => $text,
            'a' => array_merge($text, ['href', 'target', 'rel']),
            'img' => array_merge($text, ['src', 'alt', 'width', 'height', 'loading']),
            'table' => $text, 'thead' => $text, 'tbody' => $text, 'tr' => $text, 'th' => $text, 'td' => $text,
        ];
        $notes = $basic + [
            'input' => ['class', 'type', 'placeholder', 'aria-label', 'name', 'size', 'maxlength'],
            'textarea' => ['class', 'placeholder', 'aria-label', 'name', 'rows', 'cols', 'maxlength'],
            'label' => ['class', 'for'],
            'button' => ['class', 'type', 'aria-label'],
        ];
        $geo = ['class', 'fill', 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin', 'fill-rule', 'clip-rule', 'opacity', 'transform'];
        $svg = [
            'svg' => array_merge($geo, ['viewbox', 'xmlns', 'width', 'height', 'role', 'aria-hidden', 'aria-label', 'focusable']),
            'g' => $geo, 'path' => array_merge($geo, ['d']),
            'circle' => array_merge($geo, ['cx', 'cy', 'r']),
            'ellipse' => array_merge($geo, ['cx', 'cy', 'rx', 'ry']),
            'rect' => array_merge($geo, ['x', 'y', 'width', 'height', 'rx', 'ry']),
            'line' => array_merge($geo, ['x1', 'y1', 'x2', 'y2']),
            'polyline' => array_merge($geo, ['points']), 'polygon' => array_merge($geo, ['points']),
            'title' => [],
        ];
        return ['basic' => $basic, 'notes' => $notes, 'svg' => $svg];
    }

    function safe_html(?string $html, string $profile = 'basic'): string
    {
        $html = (string) $html;
        if (trim($html) === '' || !str_contains($html, '<')) {
            return $html;
        }
        $allowed = safe_html_profiles()[$profile] ?? safe_html_profiles()['basic'];

        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8"><div id="__safe_root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        $root = $doc->getElementById('__safe_root');
        if (!$root) {
            return htmlspecialchars($html, ENT_QUOTES, 'UTF-8');
        }
        safe_html_clean($root, $allowed);

        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }
        return $out;
    }

    function safe_html_clean(DOMNode $node, array $allowed): void
    {
        $drop = ['script', 'style', 'iframe', 'object', 'embed', 'form', 'frame', 'frameset', 'link', 'meta', 'base', 'template', 'noscript', 'foreignobject', 'use', 'animate', 'set'];
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof DOMComment) {
                $node->removeChild($child);
                continue;
            }
            if (!($child instanceof DOMElement)) {
                continue;
            }
            $tag = strtolower($child->localName ?? $child->nodeName);
            if (in_array($tag, $drop, true)) {
                $node->removeChild($child);
                continue;
            }
            if (!isset($allowed[$tag])) {
                // Unknown tag: keep its text content, drop the tag itself.
                safe_html_clean($child, $allowed);
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);
                continue;
            }
            foreach (iterator_to_array($child->attributes) as $attr) {
                $name = strtolower($attr->name);
                $value = $attr->value;
                $keep = in_array($name, $allowed[$tag], true) || (str_starts_with($name, 'data-') && $tag !== 'svg');
                if ($keep && in_array($name, ['href', 'src'], true)) {
                    $keep = safe_url($value) !== '';
                }
                if ($keep && $name === 'type' && $tag === 'input') {
                    $keep = in_array(strtolower($value), ['text', 'checkbox', 'radio'], true);
                }
                if (!$keep) {
                    $child->removeAttribute($attr->name);
                }
            }
            if ($tag === 'a' && strtolower($child->getAttribute('target')) === '_blank') {
                $child->setAttribute('rel', 'noopener noreferrer');
            }
            safe_html_clean($child, $allowed);
        }
    }

    /**
     * Returns the URL if it is a safe link target (http, https, mailto, tel, site-relative or #anchor),
     * otherwise an empty string. Blocks javascript:, data:, vbscript: and protocol-relative //host links.
     */
    function safe_url(?string $url): string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return '';
        }
        $check = strtolower(preg_replace('/[\x00-\x20]+/', '', html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        if (str_starts_with($check, '//')) {
            return '';
        }
        if (preg_match('/^[a-z][a-z0-9+.\-]*:/', $check)) {
            return preg_match('/^(https?|mailto|tel):/', $check) ? $url : '';
        }
        return $url;
    }

    /** Setting rendered as sanitised HTML (for fields that allow formatting). */
    function setting_html(string $key, string $default = '', string $profile = 'basic'): string
    {
        return safe_html(setting($key, $default), $profile);
    }

    /** Setting rendered as an escaped, safe URL for href/src attributes. */
    function setting_url(string $key, string $default = ''): string
    {
        $url = safe_url(setting($key, $default));
        if ($url === '') {
            $url = safe_url($default);
        }
        return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    }
}
