<?php
/**
 * Social share buttons for a blog post.
 * Expects: $sharePost (array with title, slug, meta_title, status)
 */
if (empty($sharePost)) {
    return;
}

require_once __DIR__ . '/blog-helper.php';

$postUrl = getBlogPostUrl($sharePost['slug'] ?? '');
$shareLinks = getPostShareLinks($sharePost);
$isPublished = ($sharePost['status'] ?? '') === 'published';
$shareId = 'share-' . (int) ($sharePost['id'] ?? 0);
?>
<div class="share-wrap" data-share-wrap>
    <button type="button" class="btn-icon share-trigger" title="Share on social media" aria-expanded="false" aria-controls="<?php echo htmlspecialchars($shareId); ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="18" cy="5" r="3"></circle>
            <circle cx="6" cy="12" r="3"></circle>
            <circle cx="18" cy="19" r="3"></circle>
            <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
            <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
        </svg>
    </button>
    <div class="share-menu" id="<?php echo htmlspecialchars($shareId); ?>" hidden>
        <div class="share-menu-header">Share article</div>
        <?php if (!$isPublished): ?>
            <p class="share-menu-note">Draft — link works after publishing</p>
        <?php endif; ?>
        <div class="share-menu-links">
            <?php foreach ($shareLinks as $key => $link): ?>
                <a href="<?php echo htmlspecialchars($link['url']); ?>"
                   class="share-link <?php echo htmlspecialchars($link['class']); ?>"
                   target="_blank"
                   rel="noopener noreferrer">
                    <?php echo htmlspecialchars($link['label']); ?>
                </a>
            <?php endforeach; ?>
            <button type="button"
                    class="share-link share-copy"
                    data-copy-url="<?php echo htmlspecialchars($postUrl); ?>">
                Copy Link
            </button>
        </div>
    </div>
</div>
