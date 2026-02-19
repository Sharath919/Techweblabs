<?php
/**
 * Blog Listing Page
 */

define('ADMIN_PANEL', true);
require_once __DIR__ . '/../../admin/config/db_config.php';

$db = getDB();

// Filters
$category = $_GET['category'] ?? '';
$tag = $_GET['tag'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Build query
$where = ["p.status = 'published'"];
$params = [];

if ($category) {
    $where[] = "c.slug = ?";
    $params[] = $category;
}

if ($tag) {
    $where[] = "t.slug = ?";
    $params[] = $tag;
}

$whereClause = "WHERE " . implode(" AND ", $where);

// Get total count
if ($category) {
    $countSql = "SELECT COUNT(DISTINCT p.id) as total 
                 FROM blog_posts p 
                 INNER JOIN blog_post_categories pc ON p.id = pc.post_id 
                 INNER JOIN blog_categories c ON pc.category_id = c.id 
                 $whereClause";
} elseif ($tag) {
    $countSql = "SELECT COUNT(DISTINCT p.id) as total 
                 FROM blog_posts p 
                 INNER JOIN blog_post_tags pt ON p.id = pt.post_id 
                 INNER JOIN blog_tags t ON pt.tag_id = t.id 
                 $whereClause";
} else {
    $countSql = "SELECT COUNT(*) as total FROM blog_posts p $whereClause";
}

$countStmt = $db->prepare($countSql);
$countStmt->execute($params);
$totalPosts = $countStmt->fetch()['total'];
$totalPages = ceil($totalPosts / $perPage);

// Get posts
if ($category) {
    $sql = "SELECT DISTINCT p.*, u.username as author_name 
            FROM blog_posts p 
            INNER JOIN blog_post_categories pc ON p.id = pc.post_id 
            INNER JOIN blog_categories c ON pc.category_id = c.id 
            LEFT JOIN admin_users u ON p.author_id = u.id 
            $whereClause
            ORDER BY p.published_at DESC 
            LIMIT $perPage OFFSET $offset";
} elseif ($tag) {
    $sql = "SELECT DISTINCT p.*, u.username as author_name 
            FROM blog_posts p 
            INNER JOIN blog_post_tags pt ON p.id = pt.post_id 
            INNER JOIN blog_tags t ON pt.tag_id = t.id 
            LEFT JOIN admin_users u ON p.author_id = u.id 
            $whereClause
            ORDER BY p.published_at DESC 
            LIMIT $perPage OFFSET $offset";
} else {
    $sql = "SELECT p.*, u.username as author_name 
            FROM blog_posts p 
            LEFT JOIN admin_users u ON p.author_id = u.id 
            $whereClause
            ORDER BY p.published_at DESC 
            LIMIT $perPage OFFSET $offset";
}

if (!empty($params)) {
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
} else {
    $stmt = $db->query($sql);
}

$posts = $stmt->fetchAll();

// SEO
$pageTitle = 'Blog | TechWebLabs';
$metaDescription = 'Read our latest articles on mobile app development, web development, and technology insights from TechWebLabs.';
if ($category) {
    $catInfo = $db->prepare("SELECT name FROM blog_categories WHERE slug = ?");
    $catInfo->execute([$category]);
    $cat = $catInfo->fetch();
    if ($cat) {
        $pageTitle = $cat['name'] . ' Articles | TechWebLabs';
        $metaDescription = 'Browse ' . $cat['name'] . ' articles and insights from TechWebLabs.';
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
  <meta charset="utf-8">
  <title><?php echo htmlspecialchars($pageTitle); ?></title>
  <meta name="description" content="<?php echo htmlspecialchars($metaDescription); ?>">
  <meta name="keywords" content="blog, mobile app development, web development, technology articles, TechWebLabs">
  <meta name="author" content="Techweblabs">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="theme-color" content="#4302b2">
  <meta name="robots" content="index, follow, max-image-preview:large">
  <link rel="canonical" href="https://techweblabs.com/blogs">

  <link rel="icon" href="https://techweblabs.com/favicon.ico" type="image/x-icon">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="css/css-bootstrap.min.css" rel="stylesheet">
  <link href="css/css-plugin.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="css/css-style.css" rel="stylesheet">
  <link href="css/css-responsive.css" rel="stylesheet">
  <link href="css/css-darkmode.css" rel="stylesheet">

  <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
  <meta property="og:description" content="<?php echo htmlspecialchars($metaDescription); ?>">
  <meta property="og:image" content="https://techweblabs.com/images/mobile-app-development.jpg">
  <meta property="og:url" content="https://techweblabs.com/blogs">
  <meta property="og:type" content="website">

  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
  <meta name="twitter:description" content="<?php echo htmlspecialchars($metaDescription); ?>">
  <meta name="twitter:image" content="https://techweblabs.com/images/mobile-app-development.jpg">

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
</head>

<body>
<?php
include_once ($_SERVER['DOCUMENT_ROOT'] . '/config.php');
?>
<?php
include (ROOT_DIR . 'homepage/header.php');
?>

<!-- Breadcrumb -->
<section class="breadcrumb-areav2" data-background="images/banner-5.jpg">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="breadcrumb-content text-center">
          <h1>Our Blog</h1>
          <ul>
            <li><a href="/">Home</a></li>
            <li>Blog</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Blog Listing -->
<section class="blog-listing-section" style="padding: 80px 0;">
  <div class="container">
    <?php if (empty($posts)): ?>
      <div class="row">
        <div class="col-lg-12 text-center" style="padding: 60px 20px;">
          <h2>No posts found</h2>
          <p>Check back soon for new articles!</p>
        </div>
      </div>
    <?php else: ?>
      <div class="row">
        <?php foreach ($posts as $post): ?>
          <div class="col-lg-4 col-md-6 mb-4">
            <div class="blog-post-card" style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.2s, box-shadow 0.2s; height: 100%;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 4px 16px rgba(0,0,0,0.15)';" onmouseout="this.style.transform=''; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.1)';">
              <?php if ($post['featured_image']): ?>
                <div style="width: 100%; height: 200px; overflow: hidden;">
                  <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" 
                       alt="<?php echo htmlspecialchars($post['title']); ?>" 
                       style="width: 100%; height: 100%; object-fit: cover;">
                </div>
              <?php else: ?>
                <div style="width: 100%; height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; padding: 20px; position: relative; overflow: hidden;">
                  <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, rgba(102, 126, 234, 0.9), rgba(118, 75, 162, 0.9));"></div>
                  <h3 style="color: white; font-size: 1.5rem; font-weight: 800; text-align: center; position: relative; z-index: 1; text-shadow: 
                    3px 3px 0px rgba(0,0,0,0.3),
                    -1px -1px 0px rgba(255,255,255,0.1),
                    1px 1px 0px rgba(255,255,255,0.1),
                    -2px -2px 0px rgba(0,0,0,0.2),
                    2px 2px 0px rgba(0,0,0,0.2);
                    line-height: 1.3;
                    margin: 0;
                    padding: 0 15px;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    word-wrap: break-word;
                    display: -webkit-box;
                    -webkit-line-clamp: 3;
                    -webkit-box-orient: vertical;
                    overflow: hidden;"><?php echo htmlspecialchars($post['title']); ?></h3>
                </div>
              <?php endif; ?>
              
              <div style="padding: 25px;">
                <h3 style="font-size: 1.25rem; font-weight: 600; color: #1f2937; margin-bottom: 15px; line-height: 1.4;">
                  <a href="/blogs/<?php echo htmlspecialchars($post['slug']); ?>" 
                     style="color: inherit; text-decoration: none; transition: color 0.2s;" 
                     onmouseover="this.style.color='#667eea';" 
                     onmouseout="this.style.color='inherit';">
                    <?php echo htmlspecialchars($post['title']); ?>
                  </a>
                </h3>
                
                <?php if ($post['excerpt']): ?>
                  <p style="color: #6b7280; font-size: 0.875rem; line-height: 1.6; margin-bottom: 20px;">
                    <?php echo htmlspecialchars(substr($post['excerpt'], 0, 120)) . (strlen($post['excerpt']) > 120 ? '...' : ''); ?>
                  </p>
                <?php endif; ?>
                
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; color: #9ca3af; padding-top: 15px; border-top: 1px solid #e5e7eb;">
                  <span>
                    <?php if ($post['published_at']): ?>
                      <?php echo date('M j, Y', strtotime($post['published_at'])); ?>
                    <?php endif; ?>
                    <?php if ($post['reading_time']): ?>
                      · <?php echo $post['reading_time']; ?> min
                    <?php endif; ?>
                  </span>
                  <a href="/blogs/<?php echo htmlspecialchars($post['slug']); ?>" 
                     style="color: #667eea; text-decoration: none; font-weight: 500; transition: color 0.2s;" 
                     onmouseover="this.style.textDecoration='underline';" 
                     onmouseout="this.style.textDecoration='none';">
                    Read more →
                  </a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      
      <?php if ($totalPages > 1): ?>
        <div class="row">
          <div class="col-lg-12">
            <div style="display: flex; justify-content: center; gap: 10px; margin-top: 50px;">
              <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?><?php echo $tag ? '&tag=' . urlencode($tag) : ''; ?>" 
                   class="btn btn-primary" style="padding: 10px 20px;">
                  Previous
                </a>
              <?php endif; ?>
              
              <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                  <a href="?page=<?php echo $i; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?><?php echo $tag ? '&tag=' . urlencode($tag) : ''; ?>" 
                     class="btn <?php echo $i == $page ? 'btn-primary' : ''; ?>" 
                     style="padding: 10px 15px; <?php echo $i == $page ? '' : 'background: white; border: 1px solid #e5e7eb; color: #374151;'; ?>">
                    <?php echo $i; ?>
                  </a>
                <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                  <span style="padding: 10px 5px;">...</span>
                <?php endif; ?>
              <?php endfor; ?>
              
              <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?><?php echo $tag ? '&tag=' . urlencode($tag) : ''; ?>" 
                   class="btn btn-primary" style="padding: 10px 20px;">
                  Next
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<?php
include (ROOT_DIR . 'homepage/footer.php');
?>

<script data-cfasync="false" src="js/cloudflare-static-email-decode.min.js"></script>
<script src="js/vendor-modernizr-3.5.0.min.js"></script>
<script src="js/6625-js-jquery.min.js"></script>
<script src="js/5786-js-bootstrap.bundle.min.js"></script>
<script src="js/2681-js-plugin.min.js"></script>
<script src="js/6161-js-preloader.js"></script>
<script src="js/7517-js-dark-mode.js"></script>
<script src="js/6889-js-main.js"></script>
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
