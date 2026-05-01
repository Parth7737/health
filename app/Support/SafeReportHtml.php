<?php

namespace App\Support;

/**
 * Strip dangerous tags/attributes for rich clinical / report fields.
 * Allowed tags only (PHP strip_tags strips attributes on allowed tags).
 */
class SafeReportHtml
{
    public const ALLOWED_TAGS = '<p><br><br/><strong><b><em><i><u><ul><ol><li><h2><h3><h4><blockquote><div><span><sub><sup><table><thead><tbody><tr><th><td><hr>';

    public static function sanitize(?string $html): string
    {
        if ($html === null) {
            return '';
        }

        $decoded = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(strip_tags($decoded, self::ALLOWED_TAGS));
    }
}
