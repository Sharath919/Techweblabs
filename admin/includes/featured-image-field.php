<?php
$featuredImageValue = $featuredImageValue ?? '';
?>
<div class="form-group featured-image-group">
    <label>Featured Image</label>

    <?php if ($featuredImageValue): ?>
        <div id="featured-image-preview" class="featured-image-preview">
            <img src="<?php echo htmlspecialchars($featuredImageValue); ?>" alt="Featured image preview">
        </div>
    <?php else: ?>
        <div id="featured-image-preview" class="featured-image-preview featured-image-preview--empty">
            <span>No image selected</span>
        </div>
    <?php endif; ?>

    <div class="featured-image-upload-row">
        <label for="featured-image-upload" class="btn featured-image-upload-btn">Upload Image</label>
        <input type="file" id="featured-image-upload" name="featured_image_upload" accept="image/jpeg,image/png,image/webp,image/gif">
        <span class="help-text">JPG, PNG, WebP, or GIF — max 5 MB</span>
    </div>

    <div class="featured-image-or">or paste image URL</div>

    <input type="url" id="featured-image" name="featured_image" value="<?php echo htmlspecialchars($featuredImageValue); ?>" placeholder="https://example.com/image.jpg">
    <div class="help-text">Used as the post thumbnail and social share image if OG image is empty</div>
</div>

<div class="form-group">
    <label for="og-image">Open Graph Image URL</label>
    <input type="url" id="og-image" name="og_image" value="<?php echo htmlspecialchars($ogImageValue ?? ''); ?>">
    <div class="help-text">Leave empty to use featured image</div>
</div>
