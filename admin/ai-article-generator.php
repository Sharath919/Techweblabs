<?php
/**
 * AI Article Generator - Trending Topics & One-Click Publishing
 */
define('ADMIN_PANEL', true);
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/db_config.php';
require_once __DIR__ . '/includes/ai-helper.php';
requireLogin();

$db = getDB();
$user = getCurrentUser();
$message = '';
$messageType = '';

// Handle AI article generation and publishing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'generate_topics') {
        // Get trending topic suggestions
        header('Content-Type: application/json');
        echo json_encode(getTrendingTopics());
        exit;
    }
    
    if ($action === 'generate_seo_ideas') {
        // Get SEO-focused blog ideas
        header('Content-Type: application/json');
        echo json_encode(generateSEOBlogIdeas());
        exit;
    }

    if ($action === 'generate_search_trending_topics') {
        // Get AI search topics trending (what people are searching for now)
        header('Content-Type: application/json');
        echo json_encode(getAISearchTrendingTopics());
        exit;
    }

    if ($action === 'search_topic_ideas') {
        $query = trim($_POST['query'] ?? '');
        header('Content-Type: application/json');
        echo json_encode(getTopicIdeasForSearch($query));
        exit;
    }
    
    if ($action === 'generate_article') {
        $topic = $_POST['topic'] ?? '';
        $keywords = $_POST['keywords'] ?? '';
        $length = $_POST['length'] ?? 'medium';
        
        if ($topic) {
            header('Content-Type: application/json');
            echo json_encode(generateArticle($topic, $keywords, $length));
            exit;
        }
    }
    
    if ($action === 'fix_seo_issues') {
        $articleData = json_decode($_POST['article_data'], true);
        if ($articleData) {
            header('Content-Type: application/json');
            echo json_encode(fixArticleSEOIssues($articleData));
            exit;
        }
    }

    if ($action === 'publish_article') {
        $articleData = json_decode($_POST['article_data'], true);
        
        if ($articleData) {
            $result = publishAIGeneratedArticle($articleData, $user['id']);
            if ($result['success']) {
                $message = 'Article published successfully!';
                $messageType = 'success';
                header('Location: posts.php?msg=' . urlencode($message));
                exit;
            } else {
                $message = $result['error'] ?? 'Failed to publish article';
                $messageType = 'error';
            }
        }
    }

    if ($action === 'bulk_publish_articles') {
        $articlesJson = $_POST['articles'] ?? '';
        $articles = $articlesJson ? json_decode($articlesJson, true) : null;
        header('Content-Type: application/json');
        if (!is_array($articles) || empty($articles)) {
            echo json_encode(['success' => false, 'error' => 'No articles to publish', 'published' => 0, 'failed' => 0, 'results' => []]);
            exit;
        }
        $published = 0;
        $failed = 0;
        $results = [];
        foreach ($articles as $article) {
            if (!empty($article['error'])) {
                $results[] = ['title' => $article['title'] ?? 'Unknown', 'success' => false, 'error' => $article['message'] ?? 'Skipped (generation error)'];
                $failed++;
                continue;
            }
            $result = publishAIGeneratedArticle($article, $user['id']);
            if ($result['success']) {
                $results[] = ['title' => $article['title'] ?? 'Untitled', 'success' => true, 'post_id' => $result['post_id'] ?? null];
                $published++;
            } else {
                $results[] = ['title' => $article['title'] ?? 'Untitled', 'success' => false, 'error' => $result['error'] ?? 'Failed to publish'];
                $failed++;
            }
        }
        echo json_encode(['success' => true, 'published' => $published, 'failed' => $failed, 'results' => $results]);
        exit;
    }
}

// Get existing categories for dropdown
$categories = $db->query("SELECT id, name, slug FROM blog_categories ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Article Generator | Admin Panel</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        .ai-generator-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .generator-section {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .section-title svg {
            width: 24px;
            height: 24px;
            color: var(--primary-color);
        }
        
        .trending-topics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .topic-card {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            border: 2px solid transparent;
            border-radius: 8px;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }
        
        .topic-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        }
        
        .topic-card.selected {
            border-color: var(--primary-color);
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.2), rgba(118, 75, 162, 0.2));
        }
        
        .topic-card .topic-card-checkbox {
            position: absolute;
            top: 0.75rem;
            left: 0.75rem;
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: var(--primary-color);
            z-index: 2;
        }
        
        .topic-card.has-checkbox .topic-title,
        .topic-card.has-checkbox .topic-keywords { padding-right: 1.5rem; }
        
        .bulk-actions-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
            padding: 1rem;
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            border: 2px solid #0ea5e9;
            border-radius: 10px;
        }
        
        .bulk-actions-bar .bulk-label { font-weight: 600; color: #0369a1; }
        
        .bulk-progress-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        
        .bulk-progress-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            max-width: 420px;
            width: 90%;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .bulk-progress-card h3 { margin: 0 0 1rem 0; color: var(--dark); }
        
        .bulk-progress-bar {
            height: 10px;
            background: #e5e7eb;
            border-radius: 5px;
            overflow: hidden;
            margin: 1rem 0;
        }
        
        .bulk-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transition: width 0.3s;
        }
        
        .bulk-results-section {
            margin-top: 2rem;
            padding: 1.5rem;
            background: #f0fdf4;
            border: 2px solid #10b981;
            border-radius: 12px;
        }
        
        .bulk-results-section h3 { margin: 0 0 1rem 0; color: #065f46; }
        
        .bulk-result-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
            padding: 1rem;
            background: white;
            border-radius: 8px;
            margin-bottom: 0.75rem;
            border: 1px solid #d1fae5;
        }
        
        .bulk-result-item .result-title { font-weight: 600; color: var(--dark); flex: 1; min-width: 200px; }
        
        .bulk-result-item .result-actions { display: flex; gap: 0.5rem; flex-wrap: wrap; }
        
        .topic-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }
        
        .topic-keywords {
            font-size: 0.875rem;
            color: var(--text-light);
            margin-bottom: 0.5rem;
        }
        
        .topic-reason {
            font-size: 0.875rem;
            color: var(--text);
            font-style: italic;
        }
        
        .topic-badge {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            background: var(--success-color);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .btn-ai {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
        }
        
        .btn-ai:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .btn-ai:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .loading-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .article-preview {
            background: #f9fafb;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 1rem;
            max-height: 500px;
            overflow-y: auto;
        }
        
        .article-preview h3 {
            margin-bottom: 1rem;
            color: var(--dark);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark);
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        .info-box {
            background: #e0f2fe;
            border-left: 4px solid var(--info-color);
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        
        .info-box p {
            margin: 0;
            color: var(--dark);
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="page-header">
                <h1>🤖 AI Article Generator</h1>
                <p>Get trending topic suggestions and generate SEO-optimized articles with one click</p>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>" style="margin-bottom: 2rem;">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <div class="ai-generator-container">
                <?php if (!function_exists('isAIConfigured') || !isAIConfigured()): ?>
                <div class="generator-section" style="background: #fef3c7; border-left: 4px solid #f59e0b;">
                    <div style="display: flex; align-items: start; gap: 1rem;">
                        <div style="font-size: 2rem;">⚠️</div>
                        <div>
                            <h3 style="margin: 0 0 0.5rem 0; color: #92400e;">AI Not Configured</h3>
                            <p style="margin: 0 0 0.5rem 0; color: #78350f;">To use AI article generation, you need to configure your OpenAI API key.</p>
                            <p style="margin: 0; color: #78350f;">
                                <strong>Setup Instructions:</strong><br>
                                1. Get your API key from <a href="https://platform.openai.com/api-keys" target="_blank">OpenAI Platform</a><br>
                                2. Edit <code>admin/config/ai-config.php</code> and add your key<br>
                                3. See <code>admin/AI_ARTICLE_GENERATOR_SETUP.md</code> for detailed instructions
                            </p>
                            <p style="margin: 1rem 0 0 0; color: #78350f;">
                                <strong>Note:</strong> The system will work with fallback topics, but AI generation requires API configuration.
                            </p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Trending Topics Section -->
                <div class="generator-section">
                    <div class="section-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>
                        Trending Topics for SEO
                    </div>
                    
                    <div class="info-box">
                        <p><strong>💡 How it works:</strong> Our AI analyzes current search trends, competitor content, and SEO opportunities to suggest topics that will help your blog rank higher in search results.</p>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem; padding: 1.25rem; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border: 2px solid #0ea5e9; border-radius: 12px;">
                        <label for="topic-ideas-search" style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #0369a1;">🔍 Search for topic ideas</label>
                        <p style="margin: 0 0 0.75rem 0; font-size: 0.875rem; color: #0c4a6e;">Type a topic or keyword (e.g. food delivery app, taxi, healthcare app) and get AI-generated related blog ideas.</p>
                        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
                            <input type="text" id="topic-ideas-search" placeholder="e.g. food delivery app, React Native, MVP development..." 
                                style="flex: 1; min-width: 220px; padding: 0.75rem 1rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;"
                                onkeydown="if(event.key==='Enter'){event.preventDefault();loadTopicIdeasFromSearch();}">
                            <button type="button" class="btn-ai" onclick="loadTopicIdeasFromSearch()" id="btn-topic-ideas-search" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                                Get related ideas
                            </button>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
                        <button class="btn-ai" onclick="loadTrendingTopics()" id="btn-load-topics">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 12a9 9 0 1 1-9-9c2.5 0 4.87.97 6.64 2.64L21 12z"></path>
                                <path d="M21 12v-4h-4"></path>
                            </svg>
                            Get Fresh Trending Topics
                        </button>
                        <button class="btn-ai" onclick="loadSEOIdeas()" id="btn-load-seo-ideas" style="background: linear-gradient(135deg, #10b981, #059669);">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                                <path d="M2 17l10 5 10-5"></path>
                                <path d="M2 12l10 5 10-5"></path>
                            </svg>
                            Get SEO Blog Ideas
                        </button>
                        <button class="btn-ai" onclick="loadSearchTrendingTopics()" id="btn-load-search-trending" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            AI Search Topics Trending
                        </button>
                        <div style="padding: 0.75rem 1rem; background: #f0f9ff; border-radius: 8px; color: #0369a1; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M12 16v-4"></path>
                                <path d="M12 8h.01"></path>
                            </svg>
                            <span>Click to get fresh, unique suggestions</span>
                        </div>
                    </div>
                    
                    <div id="topic-search-wrap" style="display: none; margin-top: 1.5rem;">
                        <label for="topic-search" style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--dark);">🔍 Search topics</label>
                        <input type="text" id="topic-search" placeholder="Search by title, keyword, or category..." 
                            style="width: 100%; max-width: 400px; padding: 0.75rem 1rem 0.75rem 2.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; background: #fff url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2220%22 height=%2220%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%236b7280%22 stroke-width=%222%22%3E%3Ccircle cx=%2211%22 cy=%2211%22 r=%228%22%3E%3C/circle%3E%3Cline x1=%2221%22 y1=%2221%22 x2=%2216.65%22 y2=%2216.65%22%3E%3C/line%3E%3C/svg%3E') no-repeat 12px center;">
                    </div>
                    
                    <div id="bulk-actions-wrap" class="bulk-actions-bar" style="display: none;">
                        <span class="bulk-label">📋 Bulk generate:</span>
                        <button type="button" class="btn-ai" onclick="selectAllTopics()" style="background: linear-gradient(135deg, #6366f1, #4f46e5); padding: 0.5rem 1rem; font-size: 0.9rem;">Select all</button>
                        <button type="button" class="btn-ai" onclick="deselectAllTopics()" style="background: linear-gradient(135deg, #64748b, #475569); padding: 0.5rem 1rem; font-size: 0.9rem;">Deselect all</button>
                        <span id="bulk-count" style="color: #0369a1; font-weight: 600;">0 selected</span>
                        <label style="display: flex; align-items: center; gap: 0.5rem; color: var(--dark); font-size: 0.9rem;">
                            Length:
                            <select id="bulk-article-length" style="padding: 0.4rem 0.75rem; border-radius: 6px; border: 1px solid #cbd5e1;">
                                <option value="short">Short</option>
                                <option value="medium" selected>Medium</option>
                                <option value="long">Long</option>
                            </select>
                        </label>
                        <button type="button" class="btn-ai" id="btn-bulk-generate" onclick="runBulkGenerate()" style="background: linear-gradient(135deg, #10b981, #059669);" disabled>
                            Generate selected (<span id="bulk-generate-n">0</span>)
                        </button>
                    </div>
                    
                    <div id="topics-container" class="trending-topics-grid" style="display: none; margin-top: 2rem;"></div>
                    
                    <div id="bulk-results-section" class="bulk-results-section" style="display: none;"></div>
                    
                    <div id="bulk-progress-overlay" class="bulk-progress-overlay" style="display: none;">
                        <div class="bulk-progress-card">
                            <h3>Generating articles...</h3>
                            <p id="bulk-progress-text">Preparing...</p>
                            <div class="bulk-progress-bar">
                                <div class="bulk-progress-fill" id="bulk-progress-fill" style="width: 0%;"></div>
                            </div>
                            <p id="bulk-progress-status" style="margin: 0; color: #6b7280; font-size: 0.9rem;"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Article Generator Section -->
                <div class="generator-section" id="article-generator" style="display: none;">
                    <div class="section-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                        </svg>
                        Generate Article
                    </div>
                    
                    <form id="article-form" onsubmit="generateArticle(event)">
                        <input type="hidden" id="selected-topic" name="topic">
                        <input type="hidden" id="selected-keywords" name="keywords">
                        
                        <div class="form-group">
                            <label>Selected Topic:</label>
                            <input type="text" id="topic-display" readonly style="background: #f3f4f6;">
                        </div>
                        
                        <div class="form-group">
                            <label>Target Keywords (comma-separated):</label>
                            <input type="text" id="target-keywords" placeholder="mobile app development, iOS development, etc.">
                        </div>
                        
                        <div class="form-group">
                            <label>Article Length:</label>
                            <select id="article-length">
                                <option value="short">Short (500-800 words)</option>
                                <option value="medium" selected>Medium (1000-1500 words)</option>
                                <option value="long">Long (2000+ words)</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn-ai" id="btn-generate">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path>
                            </svg>
                            Generate Article
                        </button>
                    </form>
                    
                    <div id="article-preview" class="article-preview" style="display: none;"></div>
                    
                    <div id="publish-section" style="display: none; margin-top: 1.5rem;">
                        <button class="btn-ai" onclick="fixSEOIssues()" id="btn-fix-seo" style="background: linear-gradient(135deg, #3b82f6, #2563eb); margin-right: 1rem;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 20h9"></path>
                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                            </svg>
                            Fix SEO Issues
                        </button>
                        <button class="btn-ai" onclick="publishArticle()" style="background: var(--success-color);">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Publish Article
                        </button>
                        <button class="btn-ai" onclick="saveAsDraft()" style="background: var(--warning-color); margin-left: 1rem;">
                            Save as Draft
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        let currentArticleData = null;
        let selectedTopic = null;
        let selectedTopicTitles = new Set();
        let bulkGeneratedArticles = [];
        
        async function loadTopicIdeasFromSearch() {
            const input = document.getElementById('topic-ideas-search');
            const query = (input && input.value || '').trim();
            if (!query) {
                alert('Please enter a topic or keyword to search (e.g. food delivery app, taxi, healthcare).');
                return;
            }
            const btn = document.getElementById('btn-topic-ideas-search');
            const container = document.getElementById('topics-container');
            btn.disabled = true;
            btn.innerHTML = '<span class="loading-spinner"></span> Finding ideas...';
            container.style.display = 'none';
            try {
                const formData = new FormData();
                formData.append('action', 'search_topic_ideas');
                formData.append('query', query);
                const response = await fetch('ai-article-generator.php', { method: 'POST', body: formData });
                const data = await response.json();
                if (data.success && data.topics && data.topics.length > 0) {
                    selectedTopicTitles.clear();
                    window.topicsDataFull = data.topics;
                    displayTopics(data.topics);
                    container.style.display = 'grid';
                    document.getElementById('topic-search-wrap').style.display = 'block';
                    document.getElementById('topic-search').value = '';
                    document.getElementById('topic-search').oninput = filterTopicsBySearch;
                    document.getElementById('bulk-results-section').style.display = 'none';
                } else {
                    alert(data.error || 'No ideas found. Try a different keyword.');
                }
            } catch (err) {
                console.error(err);
                alert('Error loading ideas. Please try again.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    Get related ideas
                `;
            }
        }
        
        async function loadTrendingTopics() {
            const btn = document.getElementById('btn-load-topics');
            const container = document.getElementById('topics-container');
            
            btn.disabled = true;
            btn.innerHTML = '<span class="loading-spinner"></span> Analyzing Trends...';
            container.style.display = 'none';
            
            try {
                const response = await fetch('ai-article-generator.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=generate_topics'
                });
                
                const data = await response.json();
                
                if (data.success && data.topics) {
                    selectedTopicTitles.clear();
                    window.topicsDataFull = data.topics;
                    displayTopics(data.topics);
                    container.style.display = 'grid';
                    document.getElementById('topic-search-wrap').style.display = 'block';
                    document.getElementById('topic-search').value = '';
                    document.getElementById('topic-search').oninput = filterTopicsBySearch;
                    document.getElementById('bulk-results-section').style.display = 'none';
                } else {
                    alert('Failed to load topics. ' + (data.error || 'Please try again.'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error loading topics. Please check your connection and try again.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 12a9 9 0 1 1-9-9c2.5 0 4.87.97 6.64 2.64L21 12z"></path>
                        <path d="M21 12v-4h-4"></path>
                    </svg>
                    Get Trending Topics
                `;
            }
        }
        
        function filterTopicsBySearch() {
            const q = (document.getElementById('topic-search').value || '').trim().toLowerCase();
            const full = window.topicsDataFull || window.topicsData || [];
            if (!full.length) return;
            const filtered = q === '' ? full : full.filter(t => {
                const title = (t.title || '').toLowerCase();
                const reason = (t.reason || '').toLowerCase();
                const category = (t.category || '').toLowerCase();
                const keywords = Array.isArray(t.keywords) ? t.keywords.join(' ').toLowerCase() : (t.keywords || '').toLowerCase();
                const angle = (t.angle || '').toLowerCase();
                return title.includes(q) || reason.includes(q) || category.includes(q) || keywords.includes(q) || angle.includes(q);
            });
            displayTopics(filtered);
        }
        
        async function loadSEOIdeas() {
            const btn = document.getElementById('btn-load-seo-ideas');
            const container = document.getElementById('topics-container');
            
            btn.disabled = true;
            btn.innerHTML = '<span class="loading-spinner"></span> Generating SEO Ideas...';
            container.style.display = 'none';
            
            try {
                const response = await fetch('ai-article-generator.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=generate_seo_ideas'
                });
                
                const data = await response.json();
                
                if (data.success && data.topics) {
                    selectedTopicTitles.clear();
                    window.topicsDataFull = data.topics;
                    displayTopics(data.topics);
                    container.style.display = 'grid';
                    document.getElementById('topic-search-wrap').style.display = 'block';
                    document.getElementById('topic-search').value = '';
                    document.getElementById('topic-search').oninput = filterTopicsBySearch;
                    document.getElementById('bulk-results-section').style.display = 'none';
                } else {
                    alert('Failed to load SEO ideas. ' + (data.error || 'Please try again.'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error loading SEO ideas. Please check your connection and try again.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                        <path d="M2 17l10 5 10-5"></path>
                        <path d="M2 12l10 5 10-5"></path>
                    </svg>
                    Get SEO Blog Ideas
                `;
            }
        }

        async function loadSearchTrendingTopics() {
            const btn = document.getElementById('btn-load-search-trending');
            const container = document.getElementById('topics-container');
            
            btn.disabled = true;
            btn.innerHTML = '<span class="loading-spinner"></span> Fetching Search Trends...';
            container.style.display = 'none';
            
            try {
                const response = await fetch('ai-article-generator.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=generate_search_trending_topics'
                });
                
                const data = await response.json();
                
                if (data.success && data.topics) {
                    selectedTopicTitles.clear();
                    window.topicsDataFull = data.topics;
                    displayTopics(data.topics);
                    container.style.display = 'grid';
                    document.getElementById('topic-search-wrap').style.display = 'block';
                    document.getElementById('topic-search').value = '';
                    document.getElementById('topic-search').oninput = filterTopicsBySearch;
                    document.getElementById('bulk-results-section').style.display = 'none';
                } else {
                    alert('Failed to load search trending topics. ' + (data.error || 'Please try again.'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error loading search trending topics. Please check your connection and try again.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    AI Search Topics Trending
                `;
            }
        }

        function displayTopics(topics) {
            const container = document.getElementById('topics-container');
            window.topicsData = topics;
            const isSelected = (t) => selectedTopicTitles.has(t.title);
            container.innerHTML = topics.map((topic, index) => {
                const selected = isSelected(topic);
                return `
                <div class="topic-card has-checkbox ${selected ? 'selected' : ''}" onclick="selectTopic(${index})" data-index="${index}">
                    <input type="checkbox" class="topic-card-checkbox" ${selected ? 'checked' : ''} onclick="event.stopPropagation(); toggleTopicSelection(${index});" title="Select for bulk generate">
                    ${topic.trending ? '<span class="topic-badge">🔥 Trending</span>' : ''}
                    ${topic.category ? `<div style="font-size: 0.75rem; color: #667eea; font-weight: 600; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px;">${topic.category}</div>` : ''}
                    <div class="topic-title">${topic.title}</div>
                    ${topic.angle ? `<div style="font-size: 0.75rem; color: #10b981; margin-bottom: 0.5rem; font-weight: 500;">📌 ${topic.angle}</div>` : ''}
                    ${topic.target_audience ? `<div style="font-size: 0.75rem; color: #f59e0b; margin-bottom: 0.5rem; font-weight: 500;">👥 Target: ${topic.target_audience}</div>` : ''}
                    <div class="topic-keywords"><strong>Keywords:</strong> ${topic.keywords ? topic.keywords.slice(0, 4).join(', ') : ''}${topic.keywords && topic.keywords.length > 4 ? '...' : ''}</div>
                    <div class="topic-reason">${topic.reason}</div>
                    ${topic.outline && topic.outline.length > 0 ? `
                        <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #e5e7eb; font-size: 0.75rem; color: #6b7280;">
                            <strong>Outline:</strong> ${topic.outline.slice(0, 2).join(' • ')}${topic.outline.length > 2 ? '...' : ''}
                        </div>
                    ` : ''}
                    ${topic.cta_focus ? `<div style="margin-top: 0.5rem; font-size: 0.75rem; color: #8b5cf6; font-weight: 500;">🎯 CTA: ${topic.cta_focus}</div>` : ''}
                </div>
            `}).join('');
            
            document.getElementById('bulk-actions-wrap').style.display = 'flex';
            updateBulkActionsUI();
        }
        
        function toggleTopicSelection(index) {
            const topic = (window.topicsData || [])[index];
            if (!topic) return;
            const title = topic.title;
            if (selectedTopicTitles.has(title)) selectedTopicTitles.delete(title);
            else selectedTopicTitles.add(title);
            const card = document.querySelector(`#topics-container .topic-card[data-index="${index}"]`);
            if (card) {
                card.classList.toggle('selected', selectedTopicTitles.has(title));
                const cb = card.querySelector('.topic-card-checkbox');
                if (cb) cb.checked = selectedTopicTitles.has(title);
            }
            updateBulkActionsUI();
        }
        
        function selectAllTopics() {
            (window.topicsData || []).forEach(t => selectedTopicTitles.add(t.title));
            displayTopics(window.topicsData || []);
        }
        
        function deselectAllTopics() {
            selectedTopicTitles.clear();
            displayTopics(window.topicsData || []);
        }
        
        function updateBulkActionsUI() {
            const n = selectedTopicTitles.size;
            const wrap = document.getElementById('bulk-actions-wrap');
            const countEl = document.getElementById('bulk-count');
            const nEl = document.getElementById('bulk-generate-n');
            const btn = document.getElementById('btn-bulk-generate');
            if (countEl) countEl.textContent = n + ' selected';
            if (nEl) nEl.textContent = n;
            if (btn) btn.disabled = n === 0;
        }
        
        async function runBulkGenerate() {
            const selected = (window.topicsData || []).filter(t => selectedTopicTitles.has(t.title));
            if (!selected.length) {
                alert('Please select at least one topic.');
                return;
            }
            const length = document.getElementById('bulk-article-length').value || 'medium';
            const overlay = document.getElementById('bulk-progress-overlay');
            const progressFill = document.getElementById('bulk-progress-fill');
            const progressText = document.getElementById('bulk-progress-text');
            const progressStatus = document.getElementById('bulk-progress-status');
            
            overlay.style.display = 'flex';
            bulkGeneratedArticles = [];
            const total = selected.length;
            
            for (let i = 0; i < selected.length; i++) {
                const topic = selected[i];
                const pct = Math.round(((i + 1) / total) * 100);
                progressFill.style.width = (i / total) * 100 + '%';
                progressText.textContent = `Generating article ${i + 1} of ${total}...`;
                progressStatus.textContent = topic.title;
                
                try {
                    const response = await fetch('ai-article-generator.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `action=generate_article&topic=${encodeURIComponent(topic.title)}&keywords=${encodeURIComponent(Array.isArray(topic.keywords) ? topic.keywords.join(', ') : (topic.keywords || ''))}&length=${encodeURIComponent(length)}`
                    });
                    const data = await response.json();
                    if (data.success && data.article) {
                        bulkGeneratedArticles.push(data.article);
                    } else {
                        bulkGeneratedArticles.push({ error: true, title: topic.title, message: data.error || 'Failed to generate' });
                    }
                } catch (err) {
                    bulkGeneratedArticles.push({ error: true, title: topic.title, message: err.message || 'Network error' });
                }
            }
            
            progressFill.style.width = '100%';
            progressText.textContent = 'Done!';
            progressStatus.textContent = total + ' article(s) generated.';
            await new Promise(r => setTimeout(r, 800));
            overlay.style.display = 'none';
            progressFill.style.width = '0%';
            
            displayBulkResults(bulkGeneratedArticles);
            document.getElementById('bulk-results-section').style.display = 'block';
            document.getElementById('bulk-results-section').scrollIntoView({ behavior: 'smooth' });
        }
        
        function displayBulkResults(articles) {
            const section = document.getElementById('bulk-results-section');
            if (!articles || !articles.length) {
                section.innerHTML = '<p>No articles generated.</p>';
                return;
            }
            const publishableCount = articles.filter(a => !a.error).length;
            section.innerHTML = `
                <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <h3 style="margin: 0;">✅ Generated ${articles.length} article(s)</h3>
                    ${publishableCount > 0 ? `
                        <button type="button" class="btn-ai" onclick="runBulkPublish()" style="background: linear-gradient(135deg, #10b981, #059669); padding: 0.5rem 1rem;">
                            Publish all (${publishableCount})
                        </button>
                    ` : ''}
                </div>
                ${articles.map((art, idx) => {
                    if (art.error) {
                        return `<div class="bulk-result-item" style="border-color: #fecaca;">
                            <span class="result-title">❌ ${art.title || 'Unknown'}: ${art.message || 'Error'}</span>
                        </div>`;
                    }
                    return `<div class="bulk-result-item">
                        <span class="result-title">${art.title || 'Untitled'} (${art.word_count || 0} words)</span>
                        <div class="result-actions">
                            <button type="button" class="btn-ai" onclick="viewBulkArticle(${idx})" style="padding: 0.4rem 0.75rem; font-size: 0.85rem; background: linear-gradient(135deg, #3b82f6, #2563eb);">View</button>
                            <button type="button" class="btn-ai" onclick="publishBulkArticle(${idx})" style="padding: 0.4rem 0.75rem; font-size: 0.85rem; background: var(--success-color);">Publish</button>
                            <button type="button" class="btn-ai" onclick="saveBulkArticleDraft(${idx})" style="padding: 0.4rem 0.75rem; font-size: 0.85rem; background: var(--warning-color);">Save draft</button>
                        </div>
                    </div>`;
                }).join('')}
            `;
        }
        
        function viewBulkArticle(index) {
            const art = bulkGeneratedArticles[index];
            if (!art || art.error) return;
            currentArticleData = art;
            document.getElementById('article-generator').style.display = 'block';
            document.getElementById('article-preview').style.display = 'block';
            document.getElementById('publish-section').style.display = 'block';
            displayArticlePreview(art);
            document.getElementById('article-generator').scrollIntoView({ behavior: 'smooth' });
        }
        
        function publishBulkArticle(index) {
            const art = bulkGeneratedArticles[index];
            if (!art || art.error) return;
            currentArticleData = art;
            publishArticle();
        }
        
        function saveBulkArticleDraft(index) {
            const art = bulkGeneratedArticles[index];
            if (!art || art.error) return;
            currentArticleData = { ...art, status: 'draft' };
            publishArticle();
        }

        async function runBulkPublish() {
            const toPublish = bulkGeneratedArticles.filter(a => !a.error);
            if (!toPublish.length) {
                alert('No articles to publish. Only successfully generated articles can be published.');
                return;
            }
            if (!confirm(`Publish all ${toPublish.length} article(s) now? They will be live on your website.`)) return;

            const overlay = document.getElementById('bulk-progress-overlay');
            const progressFill = document.getElementById('bulk-progress-fill');
            const progressText = document.getElementById('bulk-progress-text');
            const progressStatus = document.getElementById('bulk-progress-status');
            const overlayTitle = overlay ? overlay.querySelector('h3') : null;
            if (overlayTitle) overlayTitle.textContent = 'Publishing articles...';
            overlay.style.display = 'flex';
            progressFill.style.width = '0%';
            progressText.textContent = 'Sending request...';
            progressStatus.textContent = '';

            try {
                const formData = new FormData();
                formData.append('action', 'bulk_publish_articles');
                formData.append('articles', JSON.stringify(toPublish));
                const response = await fetch('ai-article-generator.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                progressFill.style.width = '100%';
                progressText.textContent = 'Done!';
                progressStatus.textContent = `Published ${data.published || 0}, failed ${data.failed || 0}.`;
                await new Promise(r => setTimeout(r, 800));
                overlay.style.display = 'none';
                progressFill.style.width = '0%';
                if (overlayTitle) overlayTitle.textContent = 'Generating articles...';

                if (data.success) {
                    const msg = data.failed > 0
                        ? `Published ${data.published} article(s). ${data.failed} failed.`
                        : `All ${data.published} article(s) published successfully!`;
                    alert(msg);
                    if (data.published > 0) {
                        if (confirm('View blog posts list?')) {
                            window.location.href = 'posts.php?msg=' + encodeURIComponent(msg);
                        }
                    }
                } else {
                    alert('Bulk publish failed: ' + (data.error || 'Unknown error'));
                }
            } catch (err) {
                overlay.style.display = 'none';
                progressFill.style.width = '0%';
                if (overlayTitle) overlayTitle.textContent = 'Generating articles...';
                console.error(err);
                alert('Error publishing articles. Please try again.');
            }
        }
        
        function selectTopic(index) {
            // Remove previous selection
            document.querySelectorAll('.topic-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Select new topic
            const card = document.querySelector(`[data-index="${index}"]`);
            card.classList.add('selected');
            
            selectedTopic = window.topicsData[index];
            document.getElementById('selected-topic').value = selectedTopic.title;
            document.getElementById('selected-keywords').value = selectedTopic.keywords.join(', ');
            document.getElementById('topic-display').value = selectedTopic.title;
            document.getElementById('target-keywords').value = selectedTopic.keywords.join(', ');
            
            // Show article generator
            document.getElementById('article-generator').style.display = 'block';
            document.getElementById('article-generator').scrollIntoView({ behavior: 'smooth' });
        }
        
        async function generateArticle(event) {
            event.preventDefault();
            
            const btn = document.getElementById('btn-generate');
            const preview = document.getElementById('article-preview');
            const publishSection = document.getElementById('publish-section');
            
            if (!selectedTopic) {
                alert('Please select a topic first.');
                return;
            }
            
            const keywords = document.getElementById('target-keywords').value || selectedTopic.keywords.join(', ');
            const length = document.getElementById('article-length').value;
            
            btn.disabled = true;
            btn.innerHTML = '<span class="loading-spinner"></span> Generating Article...';
            preview.style.display = 'none';
            publishSection.style.display = 'none';
            
            try {
                const response = await fetch('ai-article-generator.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=generate_article&topic=${encodeURIComponent(selectedTopic.title)}&keywords=${encodeURIComponent(keywords)}&length=${encodeURIComponent(length)}`
                });
                
                const data = await response.json();
                
                if (data.success && data.article) {
                    currentArticleData = data.article;
                    displayArticlePreview(data.article);
                    preview.style.display = 'block';
                    publishSection.style.display = 'block';
                } else {
                    alert('Failed to generate article. ' + (data.error || 'Please try again.'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error generating article. Please check your connection and try again.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path>
                    </svg>
                    Generate Article
                `;
            }
        }
        
        function displayArticlePreview(article) {
            const preview = document.getElementById('article-preview');
            const seoScore = article.seo_score || {};
            const scorePercent = seoScore.percentage || 0;
            const grade = seoScore.grade || 'N/A';
            const gradeColor = seoScore.grade_color || '#6b7280';
            
            let seoScoreHtml = '';
            if (seoScore.score !== undefined) {
                seoScoreHtml = `
                    <div style="background: ${gradeColor}15; border: 2px solid ${gradeColor}; border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <div>
                                <h3 style="margin: 0; color: ${gradeColor};">SEO Score: ${scorePercent}%</h3>
                                <div style="font-size: 2rem; font-weight: 700; color: ${gradeColor}; margin-top: 0.5rem;">Grade: ${grade}</div>
                            </div>
                            <div style="font-size: 3rem; font-weight: 700; color: ${gradeColor};">${scorePercent}%</div>
                        </div>
                        <div style="background: #f3f4f6; height: 8px; border-radius: 4px; overflow: hidden; margin-bottom: 1rem;">
                            <div style="background: ${gradeColor}; height: 100%; width: ${scorePercent}%; transition: width 0.3s;"></div>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; font-size: 0.875rem;">
                            <div><strong>Word Count:</strong> ${seoScore.word_count || article.word_count || 0}</div>
                            <div><strong>Internal Links:</strong> ${seoScore.internal_links || 0}</div>
                            <div><strong>Keyword Density:</strong> ${seoScore.keyword_density || 0}%</div>
                        </div>
                        ${seoScore.issues && seoScore.issues.length > 0 ? `
                            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                                <strong style="color: #ef4444;">⚠️ Issues:</strong>
                                <ul style="margin: 0.5rem 0 0 0; padding-left: 1.5rem; color: #6b7280;">
                                    ${seoScore.issues.map(issue => `<li>${issue}</li>`).join('')}
                                </ul>
                            </div>
                        ` : ''}
                        ${seoScore.recommendations && seoScore.recommendations.length > 0 ? `
                            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                                <strong style="color: #3b82f6;">💡 Recommendations:</strong>
                                <ul style="margin: 0.5rem 0 0 0; padding-left: 1.5rem; color: #6b7280;">
                                    ${seoScore.recommendations.map(rec => `<li>${rec}</li>`).join('')}
                                </ul>
                            </div>
                        ` : ''}
                    </div>
                `;
            }
            
            let faqsHtml = '';
            if (article.faqs && article.faqs.length > 0) {
                faqsHtml = `
                    <div style="background: #f9fafb; border-left: 4px solid #667eea; border-radius: 4px; padding: 1.5rem; margin: 1.5rem 0;">
                        <h4 style="margin: 0 0 1rem 0; color: #1f2937;">📋 FAQs Generated (${article.faqs.length})</h4>
                        ${article.faqs.slice(0, 3).map((faq, idx) => `
                            <div style="margin-bottom: 1rem;">
                                <strong style="color: #374151;">Q${idx + 1}:</strong> ${faq.question || faq.q || ''}<br>
                                <span style="color: #6b7280; font-size: 0.9rem;">${(faq.answer || faq.a || '').substring(0, 100)}...</span>
                            </div>
                        `).join('')}
                        ${article.faqs.length > 3 ? `<div style="color: #6b7280; font-size: 0.875rem;">+ ${article.faqs.length - 3} more FAQs</div>` : ''}
                    </div>
                `;
            }
            
            let prosConsHtml = '';
            if (article.pros_cons) {
                prosConsHtml = `
                    <div style="background: #f0fdf4; border-left: 4px solid #10b981; border-radius: 4px; padding: 1.5rem; margin: 1.5rem 0;">
                        <h4 style="margin: 0 0 1rem 0; color: #1f2937;">✅ Pros & Cons Section Included</h4>
                        ${article.pros_cons.pros ? `<div><strong>Pros:</strong> ${article.pros_cons.pros.length} items</div>` : ''}
                        ${article.pros_cons.cons ? `<div><strong>Cons:</strong> ${article.pros_cons.cons.length} items</div>` : ''}
                    </div>
                `;
            }
            
            // Count internal links in content
            const internalLinksMatch = (article.content || '').match(/href=["']https?:\/\/techweblabs\.com\/[^"']+["']/gi) || [];
            const internalLinksCount = internalLinksMatch.length;
            
            // Count images in content
            const imagesMatch = (article.content || '').match(/<img[^>]*>/gi) || [];
            const imagesCount = imagesMatch.length;
            
            // Check for Table of Contents
            const hasTOC = (article.content || '').includes('table-of-contents') || 
                          (article.content || '').toLowerCase().includes('table of contents');
            
            const thumbnailUrl = article.featured_image || article.og_image || '';
            const thumbnailHtml = thumbnailUrl ? `
                    <div style="margin-bottom: 1rem;">
                        <strong>Thumbnail / Banner:</strong>
                        <img src="${thumbnailUrl.replace(/"/g, '&quot;')}" alt="Article banner" style="width: 100%; max-height: 280px; object-fit: cover; border-radius: 8px; margin-top: 0.5rem;">
                        <div style="font-size: 0.8rem; color: #6b7280; margin-top: 0.25rem;">${thumbnailUrl}</div>
                    </div>` : '';

            preview.innerHTML = `
                ${seoScoreHtml}
                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 2rem;">
                    <h3 style="margin: 0 0 1rem 0; color: #1f2937; font-size: 1.75rem;">${article.title}</h3>
                    ${thumbnailHtml}
                    <div style="margin-bottom: 1rem; padding: 1rem; background: #f9fafb; border-radius: 6px;">
                        <strong>Meta Description:</strong> <span style="color: #6b7280;">${article.meta_description}</span>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; padding: 1rem; background: #f0f9ff; border-radius: 6px;">
                        <div><strong>Focus Keyword:</strong><br><span style="color: #3b82f6;">${article.seo_focus_keyword || 'N/A'}</span></div>
                        <div><strong>Word Count:</strong><br><span>${article.word_count || 0} words</span></div>
                        <div><strong>Internal Links:</strong><br><span style="color: #10b981;">${internalLinksCount} links</span></div>
                        <div><strong>Images:</strong><br><span style="color: #10b981;">${imagesCount} images</span></div>
                        <div><strong>Reading Time:</strong><br><span>${article.reading_time || 5} min</span></div>
                    </div>
                    ${hasTOC ? `
                        <div style="background: #f0fdf4; border-left: 4px solid #10b981; border-radius: 4px; padding: 1rem; margin-bottom: 1rem;">
                            <strong style="color: #10b981;">✅ Table of Contents Included</strong>
                        </div>
                    ` : ''}
                    ${faqsHtml}
                    ${prosConsHtml}
                    <div style="margin-top: 1.5rem; padding: 1rem; background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 4px;">
                        <strong>📝 Content Preview:</strong>
                        <div style="margin-top: 0.5rem; color: #6b7280; line-height: 1.6; max-height: 300px; overflow-y: auto; padding: 1rem; background: white; border-radius: 4px;">
                            ${(article.content || '').substring(0, 1000)}...
                        </div>
                    </div>
                </div>
            `;
        }
        
        async function fixSEOIssues() {
            if (!currentArticleData) {
                alert('No article to fix. Please generate an article first.');
                return;
            }
            const btn = document.getElementById('btn-fix-seo');
            btn.disabled = true;
            btn.innerHTML = '<span class="loading-spinner"></span> Fixing SEO...';
            try {
                const formData = new FormData();
                formData.append('action', 'fix_seo_issues');
                formData.append('article_data', JSON.stringify(currentArticleData));
                const response = await fetch('ai-article-generator.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                if (data.success && data.article) {
                    currentArticleData = data.article;
                    displayArticlePreview(data.article);
                    if (data.message) alert(data.message);
                } else {
                    alert('Could not fix SEO: ' + (data.error || 'Unknown error'));
                }
            } catch (err) {
                console.error(err);
                alert('Error fixing SEO. Please try again.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 20h9"></path>
                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                    </svg>
                    Fix SEO Issues
                `;
            }
        }

        function publishArticle() {
            if (!currentArticleData) {
                alert('No article to publish. Please generate an article first.');
                return;
            }
            
            if (confirm('Publish this article now? It will be live on your website immediately.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'ai-article-generator.php';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'publish_article';
                form.appendChild(actionInput);
                
                const articleInput = document.createElement('input');
                articleInput.type = 'hidden';
                articleInput.name = 'article_data';
                articleInput.value = JSON.stringify(currentArticleData);
                form.appendChild(articleInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function saveAsDraft() {
            if (!currentArticleData) {
                alert('No article to save. Please generate an article first.');
                return;
            }
            
            currentArticleData.status = 'draft';
            publishArticle();
        }
    </script>
</body>
</html>
