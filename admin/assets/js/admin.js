// Admin Panel JavaScript

// Auto-generate slug from title
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('post-title');
    const slugInput = document.getElementById('post-slug');
    const slugGenerateBtn = document.getElementById('generate-slug');
    
    if (titleInput && slugInput) {
        // Auto-generate slug on title change
        titleInput.addEventListener('input', function() {
            if (!slugInput.dataset.manual) {
                generateSlug();
            }
        });
        
        // Manual slug edit
        slugInput.addEventListener('input', function() {
            slugInput.dataset.manual = 'true';
        });
        
        // Generate slug button
        if (slugGenerateBtn) {
            slugGenerateBtn.addEventListener('click', function(e) {
                e.preventDefault();
                generateSlug();
                slugInput.dataset.manual = 'false';
            });
        }
    }
    
    // Character counter for meta description
    const metaDescInput = document.getElementById('meta-description');
    const metaDescCounter = document.getElementById('meta-desc-counter');
    
    if (metaDescInput && metaDescCounter) {
        metaDescInput.addEventListener('input', function() {
            const length = this.value.length;
            metaDescCounter.textContent = length + ' / 160 characters';
            
            if (length > 160) {
                metaDescCounter.style.color = '#ef4444';
            } else if (length > 150) {
                metaDescCounter.style.color = '#f59e0b';
            } else {
                metaDescCounter.style.color = '#6b7280';
            }
        });
    }
    
    // Auto-generate excerpt from content
    const contentInput = document.getElementById('post-content');
    const excerptInput = document.getElementById('post-excerpt');
    const excerptGenerateBtn = document.getElementById('generate-excerpt');
    
    if (excerptGenerateBtn && contentInput && excerptInput) {
        excerptGenerateBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const content = contentInput.value;
            const textContent = content.replace(/<[^>]*>/g, '').trim();
            const excerpt = textContent.substring(0, 200);
            excerptInput.value = excerpt + (textContent.length > 200 ? '...' : '');
        });
    }
    
    // Confirm delete actions
    const deleteButtons = document.querySelectorAll('[href*="delete"], [onclick*="delete"]');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
});

function generateSlug() {
    const titleInput = document.getElementById('post-title');
    const slugInput = document.getElementById('post-slug');
    
    if (titleInput && slugInput) {
        let slug = titleInput.value
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        
        slug = slug.substring(0, 255);
        slugInput.value = slug;
    }
}

// Rich text editor placeholder (can be replaced with TinyMCE, CKEditor, etc.)
function initEditor() {
    const editor = document.getElementById('post-content');
    if (editor && typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: '#post-content',
            height: 500,
            plugins: 'lists link image code table',
            toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link image | code',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 14px; }'
        });
    }
}

// Initialize on load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initEditor);
} else {
    initEditor();
}
