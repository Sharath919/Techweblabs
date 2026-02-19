<?php
/**
 * Single Blog Post Template
 * Displays individual blog posts with SEO optimization
 */

// Get post from global scope (set by router.php) or URL parameter (from .htaccess)
$post = isset($GLOBALS['blog_post']) ? $GLOBALS['blog_post'] : null;

// If not set, try to get from URL parameter (when using .htaccess on Apache)
if (!$post) {
    // Get slug from GET parameter (from .htaccess rewrite) or parse from URL
    $slug = isset($_GET['slug']) ? $_GET['slug'] : null;
    
    // If slug not in GET, try to parse from REQUEST_URI
    if (!$slug) {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if (preg_match('#/blogs/([^/]+)#', $uri, $matches)) {
            $slug = $matches[1];
        }
    }
    
    if ($slug) {
        if (!defined('ADMIN_PANEL')) {
            define('ADMIN_PANEL', true);
        }
        require_once __DIR__ . '/../../admin/config/db_config.php';
        try {
            $db = getDB();
            $stmt = $db->prepare("SELECT * FROM blog_posts WHERE slug = ? AND status = 'published'");
            $stmt->execute([$slug]);
            $post = $stmt->fetch();
            
            if ($post) {
                // Increment view count
                $db->prepare("UPDATE blog_posts SET views = views + 1 WHERE id = ?")->execute([$post['id']]);
            }
        } catch (Exception $e) {
            error_log("Blog post error: " . $e->getMessage());
        } catch (PDOException $e) {
            error_log("Blog post PDO error: " . $e->getMessage());
        }
    }
}

// If still no post, show 404
if (!$post) {
    http_response_code(404);
    $pageTitle = 'Post Not Found | TechWebLabs';
    $metaDescription = 'The blog post you are looking for does not exist.';
    $metaKeywords = '';
    $ogImage = 'https://techweblabs.com/images/mobile-app-development.jpg';
    $canonicalUrl = 'https://techweblabs.com/blogs';
} else {
    // Get post categories and tags
    if (!defined('ADMIN_PANEL')) {
        define('ADMIN_PANEL', true);
    }
    require_once __DIR__ . '/../../admin/config/db_config.php';
    $db = getDB();

    $categories = $db->prepare("SELECT c.name, c.slug FROM blog_categories c 
                                 INNER JOIN blog_post_categories pc ON c.id = pc.category_id 
                                 WHERE pc.post_id = ?");
    $categories->execute([$post['id']]);
    $postCategories = $categories->fetchAll();

    $tags = $db->prepare("SELECT t.name, t.slug FROM blog_tags t 
                           INNER JOIN blog_post_tags pt ON t.id = pt.tag_id 
                           WHERE pt.post_id = ?");
    $tags->execute([$post['id']]);
    $postTags = $tags->fetchAll();

    // Get author info
    $author = $db->prepare("SELECT username, full_name FROM admin_users WHERE id = ?");
    $author->execute([$post['author_id']]);
    $authorInfo = $author->fetch();

    // Set SEO meta
    $pageTitle = $post['meta_title'] ?: $post['title'] . ' | TechWebLabs';
    $metaDescription = $post['meta_description'] ?: $post['excerpt'] ?: substr(strip_tags($post['content']), 0, 160);
    $metaKeywords = $post['meta_keywords'] ?: '';
    $ogImage = $post['og_image'] ?: $post['featured_image'] ?: 'https://techweblabs.com/images/mobile-app-development.jpg';
    $canonicalUrl = 'https://techweblabs.com/blogs/' . $post['slug'];
}
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
  <meta charset="utf-8">
  <title><?php echo htmlspecialchars($pageTitle); ?></title>
  <meta name="description" content="<?php echo htmlspecialchars($metaDescription); ?>">
  <?php if ($metaKeywords): ?>
  <meta name="keywords" content="<?php echo htmlspecialchars($metaKeywords); ?>">
  <?php endif; ?>
  <meta name="author" content="Techweblabs">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="theme-color" content="#4302b2">
  <meta name="robots" content="index, follow, max-image-preview:large">
  <link rel="canonical" href="<?php echo isset($canonicalUrl) ? $canonicalUrl : 'https://techweblabs.com/blogs'; ?>">
  
  <base href="/">

  <link rel="icon" href="https://techweblabs.com/favicon.ico" type="image/x-icon">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="/css/css-bootstrap.min.css" rel="stylesheet">
  <link href="/css/css-plugin.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="/css/css-style.css" rel="stylesheet">
  <link href="/css/css-responsive.css" rel="stylesheet">
  <link href="/css/css-darkmode.css" rel="stylesheet">

  <?php if ($post): ?>
  <meta property="og:title" content="<?php echo htmlspecialchars($post['title']); ?>">
  <meta property="og:description" content="<?php echo htmlspecialchars($metaDescription); ?>">
  <meta property="og:image" content="<?php echo htmlspecialchars($ogImage); ?>">
  <meta property="og:url" content="<?php echo $canonicalUrl; ?>">
  <meta property="og:type" content="article">

  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?php echo htmlspecialchars($post['title']); ?>">
  <meta name="twitter:description" content="<?php echo htmlspecialchars($metaDescription); ?>">
  <meta name="twitter:image" content="<?php echo htmlspecialchars($ogImage); ?>">

  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "<?php echo htmlspecialchars($post['schema_type'] ?: 'BlogPosting'); ?>",
    "headline": "<?php echo htmlspecialchars($post['title']); ?>",
    "description": "<?php echo htmlspecialchars($metaDescription); ?>",
    "image": "<?php echo htmlspecialchars($ogImage); ?>",
    "datePublished": "<?php echo $post['published_at']; ?>",
    "dateModified": "<?php echo $post['updated_at']; ?>",
    "author": {
      "@type": "Person",
      "name": "<?php echo htmlspecialchars($authorInfo['full_name'] ?: $authorInfo['username']); ?>"
    },
    "publisher": {
      "@type": "Organization",
      "name": "TechWebLabs",
      "logo": {
        "@type": "ImageObject",
        "url": "https://techweblabs.com/images/logo.png"
      }
    },
    "mainEntityOfPage": {
      "@type": "WebPage",
      "@id": "<?php echo $canonicalUrl; ?>"
    }
  }
  </script>
  <?php endif; ?>
  
  <!-- Hotjar Tracking Code -->
  <script>
    (function(h, o, t, j, a, r) {
      h.hj = h.hj || function() {
        (h.hj.q = h.hj.q || []).push(arguments)
      };
      h._hjSettings = {
        hjid: 3665002,
        hjsv: 6
      };
      a = o.getElementsByTagName('head')[0];
      r = o.createElement('script');
      r.async = 1;
      r.src = t + h._hjSettings.hjid + j + h._hjSettings.hjsv;
      a.appendChild(r);
    })(window, document, 'https://static.hotjar.com/c/hotjar-', '.js?sv=');
  </script>
  
  <style>
    /* Blog Post Content Styling */
    .blog-post-content {
      font-size: 18px;
      line-height: 1.8;
      color: #374151;
      max-width: 100%;
    }
    
    .blog-post-content h1,
    .blog-post-content h2,
    .blog-post-content h3,
    .blog-post-content h4,
    .blog-post-content h5,
    .blog-post-content h6 {
      font-weight: 700;
      color: #1f2937;
      margin-top: 2em;
      margin-bottom: 1em;
      line-height: 1.3;
    }
    
    .blog-post-content h1 {
      font-size: 2.5rem;
      margin-top: 0;
      border-bottom: 3px solid #667eea;
      padding-bottom: 0.5rem;
    }
    
    .blog-post-content h2 {
      font-size: 2rem;
      margin-top: 2.5em;
      margin-bottom: 1em;
      color: #050748;
      border-left: 4px solid #667eea;
      padding-left: 1rem;
    }
    
    .blog-post-content h3 {
      font-size: 1.75rem;
      margin-top: 2em;
      margin-bottom: 0.875em;
      color: #1f2937;
    }
    
    .blog-post-content h4 {
      font-size: 1.5rem;
      margin-top: 1.75em;
      margin-bottom: 0.75em;
    }
    
    .blog-post-content h5 {
      font-size: 1.25rem;
      margin-top: 1.5em;
      margin-bottom: 0.625em;
    }
    
    .blog-post-content h6 {
      font-size: 1.125rem;
      margin-top: 1.25em;
      margin-bottom: 0.5em;
    }
    
    .blog-post-content p {
      margin-bottom: 1.5em;
      font-size: 18px;
      line-height: 1.8;
      color: #4b5563;
    }
    
    .blog-post-content p:first-of-type {
      font-size: 20px;
      line-height: 1.9;
      color: #374151;
      margin-bottom: 2em;
    }
    
    .blog-post-content ul,
    .blog-post-content ol {
      margin: 1.5em 0;
      padding-left: 2em;
    }
    
    .blog-post-content ul {
      list-style-type: disc;
    }
    
    .blog-post-content ol {
      list-style-type: decimal;
    }
    
    .blog-post-content li {
      margin-bottom: 0.75em;
      line-height: 1.8;
      color: #4b5563;
      font-size: 18px;
    }
    
    .blog-post-content li ul,
    .blog-post-content li ol {
      margin-top: 0.75em;
      margin-bottom: 0.75em;
    }
    
    .blog-post-content strong,
    .blog-post-content b {
      font-weight: 700;
      color: #1f2937;
    }
    
    .blog-post-content em,
    .blog-post-content i {
      font-style: italic;
    }
    
    .blog-post-content a {
      color: #667eea;
      text-decoration: underline;
      transition: color 0.2s;
    }
    
    .blog-post-content a:hover {
      color: #764ba2;
    }
    
    .blog-post-content img {
      max-width: 100%;
      height: auto;
      border-radius: 8px;
      margin: 2em 0;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .blog-post-content blockquote {
      border-left: 4px solid #667eea;
      padding: 1.5em 2em;
      margin: 2em 0;
      background: #f9fafb;
      border-radius: 0 8px 8px 0;
      font-style: italic;
      color: #6b7280;
      font-size: 1.125rem;
    }
    
    .blog-post-content blockquote p {
      margin-bottom: 0.5em;
    }
    
    .blog-post-content blockquote p:last-child {
      margin-bottom: 0;
    }
    
    .blog-post-content code {
      background: #f3f4f6;
      padding: 0.2em 0.4em;
      border-radius: 4px;
      font-size: 0.9em;
      color: #e11d48;
      font-family: 'Courier New', monospace;
    }
    
    .blog-post-content pre {
      background: #1f2937;
      color: #f9fafb;
      padding: 1.5em;
      border-radius: 8px;
      overflow-x: auto;
      margin: 2em 0;
      font-size: 0.95rem;
      line-height: 1.6;
    }
    
    .blog-post-content pre code {
      background: transparent;
      color: #f9fafb;
      padding: 0;
    }
    
    .blog-post-content table {
      width: 100%;
      border-collapse: collapse;
      margin: 2em 0;
      font-size: 16px;
    }
    
    .blog-post-content table th,
    .blog-post-content table td {
      padding: 0.75em 1em;
      border: 1px solid #e5e7eb;
      text-align: left;
    }
    
    .blog-post-content table th {
      background: #f9fafb;
      font-weight: 600;
      color: #1f2937;
    }
    
    .blog-post-content table tr:nth-child(even) {
      background: #f9fafb;
    }
    
    .blog-post-content hr {
      border: none;
      border-top: 2px solid #e5e7eb;
      margin: 3em 0;
    }
    
    /* Table of Contents Styling */
    .blog-post-content .table-of-contents {
      background: #f9fafb;
      border: 1px solid #e5e7eb;
      border-left: 4px solid #667eea;
      border-radius: 8px;
      padding: 1.5rem;
      margin: 2em 0;
    }
    
    .blog-post-content .table-of-contents h3 {
      margin: 0 0 1rem 0;
      color: #1f2937;
      font-size: 1.25rem;
      border: none;
      padding: 0;
    }
    
    .blog-post-content .table-of-contents ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    
    .blog-post-content .table-of-contents li {
      margin-bottom: 0.5rem;
      line-height: 1.6;
    }
    
    .blog-post-content .table-of-contents li li {
      padding-left: 1.5rem;
      font-size: 0.95rem;
    }
    
    .blog-post-content .table-of-contents a {
      color: #667eea;
      text-decoration: none;
      display: block;
      padding: 0.5rem 0;
      transition: color 0.2s, padding-left 0.2s;
    }
    
    .blog-post-content .table-of-contents a:hover {
      color: #764ba2;
      padding-left: 0.5rem;
    }
    
    /* Smooth scroll for TOC links */
    html {
      scroll-behavior: smooth;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
      .blog-post-single {
        padding: 1.5rem !important;
      }
      
      .blog-post-content {
        font-size: 16px;
      }
      
      .blog-post-content h1 {
        font-size: 2rem;
      }
      
      .blog-post-content h2 {
        font-size: 1.75rem;
      }
      
      .blog-post-content h3 {
        font-size: 1.5rem;
      }
      
      .blog-post-content p {
        font-size: 16px;
      }
      
      .blog-post-content p:first-of-type {
        font-size: 18px;
      }
      
      .blog-post-section {
        padding: 40px 0 !important;
      }
    }
  </style>
</head>

<body>
<?php
include_once ($_SERVER['DOCUMENT_ROOT'] . '/config.php');
?>
<?php
include (ROOT_DIR . 'homepage/header.php');
?>

<?php if (!$post): ?>
  <section class="breadcrumb-areav2" data-background="images/banner-5.jpg">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="breadcrumb-content text-center" style="padding: 100px 0;">
            <h1>Post Not Found</h1>
            <p>The blog post you are looking for does not exist or has been removed.</p>
            <a href="/blogs" class="btn btn-primary">← Back to Blog</a>
          </div>
        </div>
      </div>
    </div>
  </section>
<?php else: ?>
  <!-- Breadcrumb -->
  <section class="breadcrumb-areav2" data-background="images/banner-5.jpg">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="breadcrumb-content text-center">
            <h1><?php echo htmlspecialchars($post['title']); ?></h1>
            <ul>
              <li><a href="/">Home</a></li>
              <li><a href="/blogs">Blog</a></li>
              <li><?php echo htmlspecialchars($post['title']); ?></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Blog Post Content -->
  <section class="row_am blog-post-section" style="padding: 80px 0;">
    <div class="container">
      <div class="row">
        <div class="col-lg-8 offset-lg-2">
          <article class="blog-post-single" style="background: white; padding: 3rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <!-- Post Meta -->
            <div class="blog-post-meta" style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #e5e7eb;">
              <div style="display: flex; flex-wrap: wrap; gap: 20px; color: #6b7280; font-size: 0.875rem; margin-bottom: 15px;">
                <?php if ($authorInfo): ?>
                  <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($authorInfo['full_name'] ?: $authorInfo['username']); ?></span>
                <?php endif; ?>
                
                <?php if ($post['published_at']): ?>
                  <span><i class="fas fa-calendar"></i> <?php echo date('F j, Y', strtotime($post['published_at'])); ?></span>
                <?php endif; ?>
                
                <?php if ($post['reading_time']): ?>
                  <span><i class="fas fa-clock"></i> <?php echo $post['reading_time']; ?> min read</span>
                <?php endif; ?>
                
                <span><i class="fas fa-eye"></i> <?php echo number_format($post['views']); ?> views</span>
              </div>
              
              <?php if ($postCategories): ?>
                <div class="blog-post-categories" style="margin-top: 15px;">
                  <?php foreach ($postCategories as $cat): ?>
                    <a href="/blogs?category=<?php echo urlencode($cat['slug']); ?>" 
                       class="btn btn-primary" 
                       style="display: inline-block; padding: 6px 12px; margin-right: 8px; margin-bottom: 8px; font-size: 0.875rem; text-decoration: none;">
                      <?php echo htmlspecialchars($cat['name']); ?>
                    </a>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
            
            <!-- Featured Image -->
            <?php if ($post['featured_image']): ?>
              <div class="blog-post-featured-image" style="margin-bottom: 40px; border-radius: 8px; overflow: hidden;">
                <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" 
                     alt="<?php echo htmlspecialchars($post['title']); ?>" 
                     style="width: 100%; height: auto;">
              </div>
            <?php else: ?>
              <div class="blog-post-featured-image" style="margin-bottom: 40px; border-radius: 8px; overflow: hidden; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 300px; display: flex; align-items: center; justify-content: center; padding: 40px; position: relative;">
                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, rgba(102, 126, 234, 0.95), rgba(118, 75, 162, 0.95));"></div>
                <h1 style="color: white; font-size: 2.5rem; font-weight: 900; text-align: center; position: relative; z-index: 1; text-shadow: 
                  4px 4px 0px rgba(0,0,0,0.4),
                  -2px -2px 0px rgba(255,255,255,0.1),
                  2px 2px 0px rgba(255,255,255,0.1),
                  -3px -3px 0px rgba(0,0,0,0.3),
                  3px 3px 0px rgba(0,0,0,0.3),
                  -1px -1px 2px rgba(0,0,0,0.5),
                  1px 1px 2px rgba(0,0,0,0.5);
                  line-height: 1.2;
                  margin: 0;
                  padding: 0 30px;
                  text-transform: uppercase;
                  letter-spacing: 1px;
                  word-wrap: break-word;
                  max-width: 900px;"><?php echo htmlspecialchars($post['title']); ?></h1>
              </div>
            <?php endif; ?>
            
            <!-- Excerpt -->
            <?php if ($post['excerpt']): ?>
              <div class="blog-post-excerpt" style="font-size: 1.25rem; color: #6b7280; font-style: italic; margin-bottom: 40px; padding: 20px; background: #f9fafb; border-left: 4px solid #667eea; border-radius: 4px;">
                <?php echo htmlspecialchars($post['excerpt']); ?>
              </div>
            <?php endif; ?>
            
            <!-- Content -->
            <div class="blog-post-content" style="font-size: 1.125rem; line-height: 1.8; color: #374151; max-width: 100%; word-wrap: break-word;">
              <?php echo $post['content']; ?>
            </div>
            
            <!-- Tags -->
            <?php if ($postTags): ?>
              <div class="blog-post-tags" style="margin-top: 50px; padding-top: 30px; border-top: 1px solid #e5e7eb;">
                <strong style="display: block; margin-bottom: 15px; color: #1f2937;">Tags:</strong>
                <?php foreach ($postTags as $tag): ?>
                  <a href="/blogs?tag=<?php echo urlencode($tag['slug']); ?>" 
                     style="display: inline-block; padding: 6px 12px; margin: 5px 5px 5px 0; background: #f3f4f6; color: #374151; text-decoration: none; border-radius: 6px; font-size: 0.875rem; transition: background 0.2s;"
                     onmouseover="this.style.background='#e5e7eb';" 
                     onmouseout="this.style.background='#f3f4f6';">
                    #<?php echo htmlspecialchars($tag['name']); ?>
                  </a>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
            
            <!-- Back to Blog Link -->
            <div style="margin-top: 50px; padding-top: 30px; border-top: 1px solid #e5e7eb; text-align: center;">
              <a href="/blogs" class="btn btn-primary">← Back to Blog</a>
            </div>
          </article>
        </div>
      </div>
    </div>
  </section>
<?php endif; ?>

<?php
include (ROOT_DIR . 'homepage/footer.php');
?>

<script data-cfasync="false" src="/js/cloudflare-static-email-decode.min.js"></script>
<script src="/js/vendor-modernizr-3.5.0.min.js"></script>
<script src="/js/6625-js-jquery.min.js"></script>
<script src="/js/5786-js-bootstrap.bundle.min.js"></script>
<script src="/js/2681-js-plugin.min.js"></script>
<script src="/js/6161-js-preloader.js"></script>
<script src="/js/7517-js-dark-mode.js"></script>
<script src="/js/6889-js-main.js"></script>
<script>
  $(document).ready(function() {
    $(".leadbtn").click(function() {
      $('#leadModal').modal('show');
    });
  });
</script>
<script type="text/javascript">
  var Tawk_API = Tawk_API || {},
    Tawk_LoadStart = new Date();
  (function() {
    var s1 = document.createElement("script"),
      s0 = document.getElementsByTagName("script")[0];
    s1.async = true;
    s1.src = 'https://embed.tawk.to/5bead5ea0e6b3311cb790f0c/default';
    s1.charset = 'UTF-8';
    s1.setAttribute('crossorigin', '*');
    s0.parentNode.insertBefore(s1, s0);
  })();
</script>
</body>
</html>
