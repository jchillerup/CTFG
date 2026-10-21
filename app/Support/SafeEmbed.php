<?php

namespace App\Support;

/**
 * Renders embed snippets that originate from Airtable (untrusted) content.
 *
 * The stored snippets (has_iframe_embed / has_twitter_feed) are extracted from
 * raw listing HTML, so they must never be echoed verbatim: an Airtable editor
 * (or a compromised Airtable account) could otherwise inject arbitrary script
 * into every visitor's browser. We rebuild a minimal tag from scratch and
 * only allow embeds from known providers over HTTPS.
 */
class SafeEmbed
{
    private const ALLOWED_IFRAME_HOSTS = [
        'youtube.com',
        'www.youtube.com',
        'youtube-nocookie.com',
        'www.youtube-nocookie.com',
        'player.vimeo.com',
        'docs.google.com',
        'www.google.com',
        'google.com',
        'www.slideshare.net',
        'slideshare.net',
    ];

    /** Return a rebuilt <iframe> for the first allowed embed found in $html, or ''. */
    public static function iframe(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_use_internal_errors($previous);

        foreach ($dom->getElementsByTagName('iframe') as $iframe) {
            $src = trim((string) $iframe->getAttribute('src'));
            if (!self::isAllowedEmbedUrl($src)) {
                continue;
            }

            $attrs = ' src="' . e($src) . '"';
            foreach (['width', 'height'] as $dimension) {
                $value = $iframe->getAttribute($dimension);
                if (is_numeric($value)) {
                    $attrs .= ' ' . $dimension . '="' . (int) $value . '"';
                }
            }
            $attrs .= ' loading="lazy" sandbox="allow-scripts allow-same-origin allow-presentation" referrerpolicy="no-referrer" allowfullscreen';

            return '<iframe' . $attrs . '></iframe>';
        }

        return '';
    }

    /** Return a rebuilt Twitter timeline anchor for an allowed URL in $html, or ''. */
    public static function twitterTimeline(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_use_internal_errors($previous);

        foreach ($dom->getElementsByTagName('a') as $anchor) {
            $href = trim((string) $anchor->getAttribute('href'));
            $host = strtolower((string) parse_url($href, PHP_URL_HOST));
            if (!str_starts_with($href, 'https://') || !in_array($host, ['twitter.com', 'www.twitter.com', 'x.com', 'www.x.com'], true)) {
                continue;
            }

            return '<a class="twitter-timeline" href="' . e($href) . '">Tweets</a>';
        }

        return '';
    }

    private static function isAllowedEmbedUrl(string $url): bool
    {
        if (!str_starts_with($url, 'https://')) {
            return false;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        return in_array($host, self::ALLOWED_IFRAME_HOSTS, true);
    }
}
