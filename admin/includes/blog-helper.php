<?php
/**
 * Blog post URL and social sharing helpers
 */

if (!defined('ADMIN_PANEL')) {
    die('Direct access not allowed');
}

require_once __DIR__ . '/upload-helper.php';

/**
 * Public URL for a blog post.
 */
function getBlogPostUrl($slug) {
    $slug = trim((string) $slug);
    if ($slug === '') {
        return getSiteBaseUrl() . '/blogs';
    }

    return getSiteBaseUrl() . '/blogs/' . rawurlencode($slug);
}

/**
 * Build social share links for a post.
 *
 * @return array<string, array{label: string, url: string, class: string}>
 */
function getPostShareLinks($post) {
    $title = $post['meta_title'] ?? $post['title'] ?? 'TechWebLabs Blog';
    $url = getBlogPostUrl($post['slug'] ?? '');
    $encodedUrl = rawurlencode($url);
    $encodedTitle = rawurlencode($title);

    return [
        'facebook' => [
            'label' => 'Facebook',
            'url' => 'https://www.facebook.com/sharer/sharer.php?u=' . $encodedUrl,
            'class' => 'share-facebook',
        ],
        'twitter' => [
            'label' => 'X (Twitter)',
            'url' => 'https://twitter.com/intent/tweet?url=' . $encodedUrl . '&text=' . $encodedTitle,
            'class' => 'share-twitter',
        ],
        'linkedin' => [
            'label' => 'LinkedIn',
            'url' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $encodedUrl,
            'class' => 'share-linkedin',
        ],
        'whatsapp' => [
            'label' => 'WhatsApp',
            'url' => 'https://wa.me/?text=' . rawurlencode($title . ' ' . $url),
            'class' => 'share-whatsapp',
        ],
        'telegram' => [
            'label' => 'Telegram',
            'url' => 'https://t.me/share/url?url=' . $encodedUrl . '&text=' . $encodedTitle,
            'class' => 'share-telegram',
        ],
        'email' => [
            'label' => 'Email',
            'url' => 'mailto:?subject=' . $encodedTitle . '&body=' . rawurlencode('Read this article: ' . $url),
            'class' => 'share-email',
        ],
    ];
}
