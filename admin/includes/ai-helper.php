<?php
/**
 * AI Helper Functions for Article Generation
 */

require_once __DIR__ . '/../config/ai-config.php';
require_once __DIR__ . '/seo-helper.php';

/**
 * Generate SEO-focused blog ideas using specialized prompt
 */
function generateSEOBlogIdeas() {
    // Check if AI is configured
    if (!isAIConfigured()) {
        return [
            'success' => false,
            'error' => 'AI is not configured. Please set up OpenAI API key.'
        ];
    }
    
    try {
        $prompt = 'You are an expert SEO strategist and technology content planner for a software development company.

Company Name: TechWebLabs  
Industry: Mobile App Development & Web Development  
Target Audience:
- Startups
- Business Owners
- Founders
- Product Managers
- Enterprises

Goal:
Generate high-ranking, lead-driven blog content ideas that attract users searching for mobile app and web development services.

Primary Focus Keywords:
- best mobile app and web development company
- mobile app development company in India
- web development services
- custom software development
- startup app development

Instructions:
Generate SEO blog ideas similar to:
- technology comparisons (e.g., Flutter vs React Native)
- cost & pricing guides
- startup & MVP development guides
- business decision blogs (web vs mobile apps)
- industry trends
- UI/UX best practices
- outsourcing & Indian IT advantages
- post-launch maintenance & support
- AI and emerging technologies in app development

Return STRICTLY valid JSON in the following structure:

{
  "blog_ideas": [
    {
      "category": "",
      "title": "",
      "search_intent": "",
      "primary_keyword": "",
      "secondary_keywords": [],
      "target_audience": "",
      "brief_outline": [
        ""
      ],
      "cta_focus": ""
    }
  ]
}

Rules:
- Provide 10 to 15 high-quality blog ideas
- Each idea must be SEO-focused and conversion-oriented
- Use professional, business-friendly language
- Titles should be optimized for Google search
- Do NOT include markdown
- Do NOT include explanations
- Output ONLY valid JSON';
        
        $response = callOpenAI($prompt, 2000);
        
        if ($response && isset($response['choices'][0]['message']['content'])) {
            $content = $response['choices'][0]['message']['content'];
            
            // Extract JSON from response
            $jsonStart = strpos($content, '{');
            $jsonEnd = strrpos($content, '}') + 1;
            
            if ($jsonStart !== false && $jsonEnd !== false) {
                $jsonContent = substr($content, $jsonStart, $jsonEnd - $jsonStart);
                $ideasData = json_decode($jsonContent, true);
                
                if ($ideasData && isset($ideasData['blog_ideas'])) {
                    // Convert to topic format for compatibility
                    $topics = array_map(function($idea) {
                        return [
                            'title' => $idea['title'] ?? '',
                            'keywords' => array_merge(
                                [$idea['primary_keyword'] ?? ''],
                                $idea['secondary_keywords'] ?? []
                            ),
                            'reason' => ($idea['search_intent'] ?? '') . ' | Target: ' . ($idea['target_audience'] ?? '') . ' | CTA: ' . ($idea['cta_focus'] ?? ''),
                            'trending' => true,
                            'category' => $idea['category'] ?? 'SEO Blog Idea',
                            'angle' => $idea['search_intent'] ?? 'SEO-Optimized',
                            'outline' => $idea['brief_outline'] ?? [],
                            'target_audience' => $idea['target_audience'] ?? '',
                            'cta_focus' => $idea['cta_focus'] ?? ''
                        ];
                    }, $ideasData['blog_ideas']);
                    
                    return [
                        'success' => true,
                        'topics' => $topics
                    ];
                }
            }
        }
        
        return [
            'success' => false,
            'error' => 'Failed to parse AI response'
        ];
    } catch (Exception $e) {
        error_log("SEO Blog Ideas Generation Error: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'Error generating blog ideas: ' . $e->getMessage()
        ];
    }
}

/**
 * Get AI-powered trending search topics (what people are searching for right now)
 */
function getAISearchTrendingTopics() {
    if (!isAIConfigured()) {
        return [
            'success' => true,
            'topics' => getFallbackTopics()
        ];
    }

    try {
        $currentDate = date('F Y');
        $prompt = "You are an expert at identifying CURRENT search trends and what people are actively searching for on Google in the technology and software development space.

Current date: $currentDate

Generate 8-10 blog article topics based on TRENDING SEARCH QUERIES—what users are actually typing into search engines RIGHT NOW in these areas:
- Mobile app development (iOS, Android, Flutter, React Native)
- Web development and frameworks
- On-demand apps (food delivery, taxi, grocery, healthcare)
- AI/ML in apps, ChatGPT integration, automation
- Startup/MVP development, cost guides
- E-commerce and marketplace apps
- UI/UX, app store optimization (ASO)
- Location: India, Hyderabad (where relevant)

Focus on:
1. Rising search terms and seasonal trends (e.g., \"cost in 2026\", \"best practices 2026\")
2. Question-style queries (\"how to\", \"what is\", \"best way to\")
3. Comparison searches (\"X vs Y\", \"which is better\")
4. Commercial intent (\"cost\", \"price\", \"hire\", \"company\")
5. Trending tech (AI, no-code, low-code, specific frameworks)

For each topic provide:
- title: SEO-optimized, 50-70 chars, include primary keyword
- keywords: 4-6 relevant search terms (include long-tail)
- reason: why this is trending in search right now / search intent
- trending: true
- category: short category name
- angle: e.g. \"Trending Search\", \"Cost\", \"How-to\", \"Comparison\"

Return STRICTLY valid JSON only:
{
  \"topics\": [
    {
      \"title\": \"...\",
      \"keywords\": [\"...\"],
      \"reason\": \"...\",
      \"trending\": true,
      \"category\": \"...\",
      \"angle\": \"...\"
    }
  ]
}

Rules: 8-10 topics, valid JSON only, no markdown, no explanations outside JSON.";

        $response = callOpenAI($prompt, 2000);

        if ($response && isset($response['choices'][0]['message']['content'])) {
            $content = $response['choices'][0]['message']['content'];
            $jsonStart = strpos($content, '{');
            $jsonEnd = strrpos($content, '}') + 1;

            if ($jsonStart !== false && $jsonEnd !== false) {
                $jsonContent = substr($content, $jsonStart, $jsonEnd - $jsonStart);
                $data = json_decode($jsonContent, true);

                if ($data && isset($data['topics']) && is_array($data['topics'])) {
                    return [
                        'success' => true,
                        'topics' => $data['topics']
                    ];
                }
            }
        }
    } catch (Exception $e) {
        error_log("AI Search Trending Topics Error: " . $e->getMessage());
    }

    return [
        'success' => true,
        'topics' => getFallbackTopics()
    ];
}

/**
 * Get related topic ideas for a search query (e.g. "food delivery app" → blog topic ideas)
 */
function getTopicIdeasForSearch($query) {
    $query = trim($query);
    if ($query === '') {
        return ['success' => false, 'error' => 'Please enter a topic or keyword to search.'];
    }
    if (!isAIConfigured()) {
        return [
            'success' => true,
            'topics' => getFallbackTopics()
        ];
    }

    try {
        $prompt = "You are an SEO content strategist for TechWebLabs (mobile app and web development company, Hyderabad, India).

The user searched for: \"$query\"

Generate 6-8 RELATED blog article topic ideas that match this theme. Ideas should be specific, SEO-friendly, and useful for attracting leads (startups, businesses looking for app development).

For each topic provide:
- title: SEO-optimized headline, 50-70 chars, include primary keyword
- keywords: array of 4-6 SEO keywords (include long-tail)
- reason: brief why this topic is valuable for the audience
- trending: true/false
- category: short category name
- angle: e.g. \"How-to\", \"Cost\", \"Comparison\", \"Features\", \"Process\"

Return STRICTLY valid JSON only:
{
  \"topics\": [
    {
      \"title\": \"...\",
      \"keywords\": [\"...\"],
      \"reason\": \"...\",
      \"trending\": true,
      \"category\": \"...\",
      \"angle\": \"...\"
    }
  ]
}

Rules: 6-8 topics, valid JSON only, no markdown.";

        $response = callOpenAI($prompt, 1500);
        if ($response && isset($response['choices'][0]['message']['content'])) {
            $content = $response['choices'][0]['message']['content'];
            $jsonStart = strpos($content, '{');
            $jsonEnd = strrpos($content, '}') + 1;
            if ($jsonStart !== false && $jsonEnd !== false) {
                $data = json_decode(substr($content, $jsonStart, $jsonEnd - $jsonStart), true);
                if ($data && isset($data['topics']) && is_array($data['topics'])) {
                    return ['success' => true, 'topics' => $data['topics']];
                }
            }
        }
    } catch (Exception $e) {
        error_log("Topic ideas search error: " . $e->getMessage());
        return ['success' => false, 'error' => $e->getMessage()];
    }
    return ['success' => false, 'error' => 'Could not generate ideas. Please try again.'];
}

/**
 * Get trending topic suggestions for SEO
 */
function getTrendingTopics() {
    // Check if AI is configured
    if (!isAIConfigured()) {
        // Return fallback topics if AI is not configured
        return [
            'success' => true,
            'topics' => getFallbackTopics()
        ];
    }
    
    try {
        // Add timestamp to ensure different topics each time
        $currentTime = date('Y-m-d H:i');
        $randomSeed = rand(1, 1000);
        
        // Get comprehensive list of services for context
        $services = getInternalLinkingOpportunities();
        $serviceList = implode(', ', array_keys($services['services']));
        
        $prompt = "Generate 8-10 UNIQUE and DIVERSE trending blog article topics for TechWebLabs (a mobile app and web development company in Hyderabad, India, with 6+ years of experience).

IMPORTANT: Generate FRESH topics different from previous requests. Focus on actionable, specific topics that drive business leads.

Current date: $currentTime
Random seed: $randomSeed

Make topics UNIQUE and VARIED across different services and angles.

COMPANY SERVICES TO CONSIDER:
- On-Demand Services: Food Delivery App, Grocery App, Taxi/Ride-Sharing App, Home Services App, Healthcare App, Professional Services App, Fitness App, Pet Care App, Beauty & Salon App, E-learning App
- Marketplace Apps: General Marketplaces, Handmade Goods, Freelance Services, Real Estate, Fashion & Apparel, Automotive, Local Services
- Business Models: UberEats Clone, DoorDash Clone, Swiggy Clone, Zomato Clone, Uber Clone, Ola Clone, Instacart Clone, BigBasket Clone, etc.
- Services: Custom App Development, iOS Development, Android Development, Cross-Platform Development (React Native, Flutter), E-commerce Website Development, UI/UX Design, App Store Optimization, MVP Development
- Solutions: ERP, CRM, HRM, CMS, E-commerce Platform, Inventory Management

TOPIC VARIETY REQUIREMENTS:
Generate topics covering different angles:
1. Development Process/Steps (e.g., \"Step-by-Step Guide to Food Delivery App Development\")
2. How-to Guides (e.g., \"How to Build a Grocery Delivery App Like BigBasket\")
3. Cost/Budget Topics (e.g., \"Food Delivery App Development Cost in 2026\")
4. Feature Lists (e.g., \"Essential Features for Healthcare Mobile Apps\")
5. Technology Comparisons (e.g., \"React Native vs Flutter: Which is Better for Startup Apps?\")
6. Industry-Specific (e.g., \"On-Demand Service App Development for Healthcare Industry\")
7. Best Practices (e.g., \"Best Practices for E-commerce App Development\")
8. Company Showcase (e.g., \"Why TechWebLabs is the Best Mobile App Development Company in Hyderabad\")
9. Trends & Future (e.g., \"Top 10 Mobile App Development Trends Shaping 2026\")
10. Case Studies/Examples (e.g., \"Successful Taxi App Development: A Complete Guide\")

For each topic, provide:
1. A compelling, SEO-optimized title (50-70 characters, include primary keyword)
2. 4-6 relevant SEO keywords (include long-tail keywords)
3. A brief explanation of why this topic is valuable for SEO and users
4. Which service/industry it relates to
5. Trending indicator (true/false)

Format as JSON with this structure:
{
  \"topics\": [
    {
      \"title\": \"Complete Guide to Food Delivery App Development Process in 2026\",
      \"keywords\": [\"food delivery app development\", \"food delivery app development process\", \"steps to build food delivery app\", \"food delivery app development cost\", \"food ordering app development\"],
      \"reason\": \"High search volume for food delivery app development with focus on process/steps. Targets businesses looking to build their own app. Includes competitive keywords.\",
      \"trending\": true,
      \"category\": \"Food Delivery App Development\",
      \"angle\": \"Process/Steps\"
    }
  ]
}

IMPORTANT:
- Generate FRESH, UNIQUE topics (avoid generic topics like \"mobile app trends\")
- Make topics SPECIFIC and ACTIONABLE:
  * Use: \"Complete Food Delivery App Development Process: 7 Steps Guide\"
  * Avoid: \"Mobile App Development\" (too generic)
- Include various formats:
  * How-to: \"How to Build a Grocery App Like BigBasket\"
  * Process: \"Step-by-Step Food Delivery App Development Process\"
  * Cost: \"Food Delivery App Development Cost in India 2026\"
  * Features: \"Essential Features for Healthcare Mobile Apps\"
  * Comparison: \"React Native vs Flutter for Startup Apps\"
  * Best Practices: \"Best Practices for E-commerce App Development\"
  * Company: \"Why Choose TechWebLabs for Mobile App Development in Hyderabad\"
- Cover ALL industries/services:
  * Food Delivery, Grocery, Taxi, Healthcare, Fitness, Beauty, E-learning
  * Real Estate, E-commerce, Marketplace apps
  * iOS, Android, React Native, Flutter
  * MVP, ASO, UI/UX Design
- Include location: Add \"in Hyderabad\" or \"in India\" where relevant
- Focus on topics that:
  * Rank well in Google search
  * Drive qualified business leads
  * Answer specific user questions
  * Showcase TechWebLabs expertise

Generate 8-10 UNIQUE topics (different from any previous requests) covering various services, angles, and industries. Make them SPECIFIC and ACTIONABLE.";
        
        $response = callOpenAI($prompt, 2000);
        
        if ($response && isset($response['choices'][0]['message']['content'])) {
            $content = $response['choices'][0]['message']['content'];
            
            // Extract JSON from response
            $jsonStart = strpos($content, '{');
            $jsonEnd = strrpos($content, '}') + 1;
            
            if ($jsonStart !== false && $jsonEnd !== false) {
                $jsonContent = substr($content, $jsonStart, $jsonEnd - $jsonStart);
                $topicsData = json_decode($jsonContent, true);
                
                if ($topicsData && isset($topicsData['topics'])) {
                    return [
                        'success' => true,
                        'topics' => $topicsData['topics']
                    ];
                }
            }
        }
    } catch (Exception $e) {
        error_log("AI Topic Generation Error: " . $e->getMessage());
    }
    
    // Fallback if AI fails
    return [
        'success' => true,
        'topics' => getFallbackTopics()
    ];
}

/**
 * Generate a complete SEO-optimized article with all enhancements
 */
function generateArticle($topic, $keywords = '', $length = 'medium', $options = []) {
    if (!isAIConfigured()) {
        return [
            'success' => false,
            'error' => 'AI is not configured. Please set up OpenAI API key in admin/config/ai-config.php'
        ];
    }
    
    try {
        $wordCounts = [
            'short' => '800-1000',
            'medium' => '1500-2000',
            'long' => '2500-3000'
        ];
        $targetWords = $wordCounts[$length] ?? '1500-2000';
        
        // Get internal linking suggestions
        $internalLinks = suggestInternalLinks('', $keywords);
        $internalLinkTexts = array_map(function($link) {
            return "[" . $link['text'] . "](" . $link['url'] . ")";
        }, $internalLinks);
        $internalLinksStr = implode(', ', array_slice($internalLinkTexts, 0, 5));
        
        // Get relevant images ONLY from allowed list (no invented URLs)
        $relevantImages = getRelevantImages($topic, $keywords);
        $imagesList = implode(', ', $relevantImages);
        $imagesListEscaped = implode("\n   - ", $relevantImages);

        // Build comprehensive prompt
     

$prompt = "Write a comprehensive, 100% SEO-optimized blog article for TechWebLabs (a leading mobile app and web development company based in Hyderabad, India, with 6+ years of experience) on the topic: \"$topic\"

Target keywords: $keywords

CRITICAL REQUIREMENTS (aim for 100% SEO score):

1. CONTENT STRUCTURE:
   - Word count: $targetWords words (comprehensive and detailed)
   - Engaging, professional, and authoritative tone
   - Introduction with hook and overview
   - Main body with multiple H2 and H3 subheadings
   - Conclusion with call-to-action
   - Use proper HTML heading hierarchy (H2 for main sections, H3 for subsections)
   - PRIMARY KEYWORD must appear naturally within the first 100 words of the article body (critical for Google ranking)

2. INTERNAL LINKING (MANDATORY - EVERY ARTICLE):
   - EVERY article MUST include at least 3-5 internal links to TechWebLabs pages. No exceptions.
   - Suggested services to link: $internalLinksStr
   - Link format: <a href=\"https://techweblabs.com/{url}\">{anchor text}</a>
   - Spread links naturally in introduction, body, and conclusion (e.g. custom-app-development, ios-app-development, food-delivery-app-development, on-demand-taxi-booking-app-development).
   - Make links contextually relevant (e.g., if discussing mobile apps, link to 'custom-app-development'; if taxi/ride, link to 'on-demand-taxi-booking-app-development').

3. TECHWEBLABS BRANDING FOR GOOGLE RANKING (MANDATORY):
   - Mention TechWebLabs by name at least 3-4 times in the content.
   - Include these ranking-focused phrases naturally (so TechWebLabs ranks in Google):
     * \"TechWebLabs is one of the best mobile app development companies in Hyderabad\"
     * \"leading mobile app development company in Hyderabad\" or \"top app development company in Hyderabad\"
     * \"best company for mobile app development in Hyderabad\" or \"trusted app development company in India\"
   - Add a clear conclusion section that positions TechWebLabs as the best choice (e.g. \"When it comes to mobile app development in Hyderabad, TechWebLabs stands out as the preferred partner...\")
   - These phrases help the TechWebLabs website rank for searches like \"best mobile app development company Hyderabad\", \"app development company in Hyderabad\"
   - Include a short 2-sentence author/company authority blurb that mentions TechWebLabs' 6+ years of experience and domain expertise, to build E-E-A-T (Experience, Expertise, Authoritativeness, Trustworthiness) trust signals with Google.

4. FAQ SECTION (REQUIRED):
   - Generate 4-6 relevant FAQs related to the topic
   - Format as structured HTML with proper questions and answers
   - Place FAQs before the conclusion

5. PROS AND CONS (IF APPLICABLE):
   - If the topic involves comparisons or choices, include a pros/cons section
   - Format as clear lists with explanations

6. SEO OPTIMIZATION (100%):
   - Title: 50-60 characters, include primary keyword in first 60 chars
   - Meta description: 150-160 characters, include focus keyword
   - Include primary keyword naturally in content (1-2% density), in at least one H2
   - Use semantic/LSI keywords: include 5-8 LSI (Latent Semantic Indexing) / semantic keywords — related terms, synonyms, and conceptually related phrases — woven naturally throughout the content. Output these in the `lsi_keywords` field.
   - Avoid keyword stuffing
   - Aim for Flesch reading ease score of 60-70: use short sentences (15-20 words average), plain language, and avoid unexplained jargon. This improves both readability and Google rankings.

7. IMAGES (STRICT - USE ONLY THIS LIST):
   - You MUST use ONLY these exact image URLs. Do NOT invent, guess, or create any other URL. Copy each URL character-for-character:
   - $imagesListEscaped
   - Include 2-3 images from the list above, placed after intro, in middle, near end
   - Format: <img src=\"EXACT_URL_FROM_LIST_ABOVE\" alt=\"DESCRIPTIVE_ALT_TEXT\" style=\"width: 100%; max-width: 800px; height: auto; border-radius: 8px; margin: 2em auto; display: block;\">
   - Alt text should be descriptive and include relevant keywords

8. TABLE OF CONTENTS (REQUIRED):
   - Generate a table of contents based on all H2 and H3 headings
   - Place it right after the introduction (before first H2)
   - Format as numbered list with anchor links
   - Include all main sections (H2) and subsections (H3)

9. CONTENT QUALITY:
   - Provide actionable insights and practical examples
   - Include statistics, data, or industry insights where relevant
   - Make it valuable for readers searching for this topic
   - Highlight TechWebLabs expertise and experience naturally

Return ONLY a JSON object with this exact structure:
{
  \"title\": \"SEO-optimized title with primary keyword (50-60 chars, include primary keyword)\",
  \"slug\": \"seo-friendly-url-slug with primary keyword, max 60 characters, lowercase, hyphens only, no stop words (e.g. 'mobile-app-development-hyderabad')\",
  \"meta_description\": \"Compelling meta description with call-to-action (150-160 chars, include primary keyword)\",
  \"meta_keywords\": \"$keywords, plus 3-5 related keywords\",
  \"seo_focus_keyword\": \"Primary keyword from the list\",
  \"lsi_keywords\": \"5-8 LSI/semantic keywords naturally related to the topic and primary keyword, comma-separated (e.g. app development cost, mobile app features, app development process, etc.)\",
  \"excerpt\": \"2-3 sentence engaging excerpt highlighting key value\",
  \"og_title\": \"Open Graph title for social sharing (same as or slight variation of main title, max 60 chars)\",
  \"og_description\": \"Open Graph description for Facebook/LinkedIn sharing (150-160 chars, include primary keyword)\",
  \"og_image\": \"One exact image URL from the allowed list above — used as the social share preview image\",
  \"twitter_title\": \"Twitter/X card title (max 60 chars, include primary keyword)\",
  \"twitter_description\": \"Twitter/X card description (max 125 chars, punchy and keyword-rich)\",
  \"schema_markup\": \"A complete JSON-LD string (escaped for JSON) containing TWO schema blocks: (1) Article schema with @type Article, headline, description, author (TechWebLabs), publisher (TechWebLabs), datePublished, and image; (2) FAQPage schema using the faqs array below with @type FAQPage and mainEntity array of Questions and Answers. Wrap both in an @graph array under a single <script type='application/ld+json'> tag.\",
  \"content\": \"Full article content in HTML format. MUST include:
    - Primary keyword in the FIRST 100 WORDS of the article body (critical)
    - Table of contents at the beginning (after intro, before first H2) with links to all headings
    - Proper <h2> and <h3> headings (each H2 and H3 needs an id attribute for TOC links: id=\\\"toc-section-name\\\")
    - <p> paragraphs
    - 2-3 images using ONLY the exact URLs from the allowed list above (no other image URLs)
    - 3-5 internal links to TechWebLabs (format: <a href=\\\"https://techweblabs.com/{url}\\\">{anchor}</a>) - MANDATORY for every article
    - TechWebLabs mentioned 3-4 times plus phrases like 'best mobile app development company in Hyderabad', 'leading app development company in Hyderabad'
    - A 2-sentence E-E-A-T authority blurb about TechWebLabs' 6+ years of experience (place near intro or conclusion)
    - FAQ section with 4-6 questions (use <h3>FAQ</h3> and structure questions/answers)
    - Pros/cons section if applicable (use <h3>Pros and Cons</h3>)
    - Conclusion stating TechWebLabs is the best choice for app development in Hyderabad/India
    - Short sentences (15-20 words avg) and plain language for readability\",
  \"word_count\": number,
  \"faqs\": [
    {\"question\": \"Question text\", \"answer\": \"Answer text\"},
    ...
  ],
  \"pros_cons\": {
    \"pros\": [\"Pro 1\", \"Pro 2\", ...],
    \"cons\": [\"Con 1\", \"Con 2\", ...]
  } (only if applicable, otherwise null)
}

IMPORTANT:
- EVERY article MUST have 3-5 internal links to https://techweblabs.com/ pages (service pages, about, etc.)
- TechWebLabs must be positioned as the best mobile app development company in Hyderabad and India for Google ranking
- Include ranking phrases naturally: best app development company Hyderabad, leading mobile app development company in Hyderabad
- Primary keyword MUST appear in the first 100 words
- Include FAQ schema + Article schema in the `schema_markup` field (JSON-LD)
- Include all social meta fields: og_title, og_description, og_image, twitter_title, twitter_description
- Include lsi_keywords field with 5-8 semantic/related keywords
- Include slug field (SEO-friendly URL, max 60 chars, hyphens only)
- Content should be comprehensive, detailed, and valuable with proper HTML structure
- Use short sentences and plain language (Flesch reading ease 60-70)";
        
        $response = callOpenAI($prompt, 4000);
        
        if ($response && isset($response['choices'][0]['message']['content'])) {
            $content = $response['choices'][0]['message']['content'];
            
            // Extract JSON from response
            $jsonStart = strpos($content, '{');
            $jsonEnd = strrpos($content, '}') + 1;
            
            if ($jsonStart !== false && $jsonEnd !== false) {
                $jsonContent = substr($content, $jsonStart, $jsonEnd - $jsonStart);
                $articleData = json_decode($jsonContent, true);
                
                if ($articleData) {
                    // Generate slug from title (using functions from auth.php)
                    if (!function_exists('generateSlug')) {
                        require_once __DIR__ . '/../config/auth.php';
                    }
                    if (!function_exists('makeSlugUnique')) {
                        require_once __DIR__ . '/../config/auth.php';
                    }
                    // Generate slug and make it unique
                    $baseSlug = generateSlug($articleData['title']);
                    $articleData['slug'] = makeSlugUnique($baseSlug);
                    $articleData['status'] = 'draft';
                    $articleData['reading_time'] = calculateReadingTime($articleData['word_count'] ?? 1000);
                    
                    // Ensure FAQs are in proper format
                    if (!isset($articleData['faqs']) || !is_array($articleData['faqs'])) {
                        $articleData['faqs'] = [];
                    }
                    
                    // Ensure pros_cons is in proper format
                    if (!isset($articleData['pros_cons']) || !is_array($articleData['pros_cons'])) {
                        $articleData['pros_cons'] = null;
                    }
                    
                    // Sanitize images: replace any non-allowed img src with allowed URLs (no 404s)
                    $articleData['content'] = sanitizeArticleImages($articleData['content'], getAllowedImageUrls());
                    
                    // Calculate SEO score
                    $seoScore = calculateSEOScore($articleData);
                    $articleData['seo_score'] = $seoScore;
                    
                    // Enhance content with FAQ section if not already in content
                    if (!empty($articleData['faqs']) && stripos($articleData['content'], 'faq') === false) {
                        $faqHtml = "\n\n<h2>Frequently Asked Questions</h2>\n";
                        foreach ($articleData['faqs'] as $faq) {
                            $faqHtml .= "<h3>" . htmlspecialchars($faq['question'] ?? $faq['q'] ?? '') . "</h3>\n";
                            $faqHtml .= "<p>" . htmlspecialchars($faq['answer'] ?? $faq['a'] ?? '') . "</p>\n";
                        }
                        $articleData['content'] .= $faqHtml;
                    }
                    
                    // Enhance content with pros/cons if applicable
                    if (!empty($articleData['pros_cons']) && is_array($articleData['pros_cons'])) {
                        $prosConsHtml = "\n\n<h2>Pros and Cons</h2>\n";
                        if (!empty($articleData['pros_cons']['pros'])) {
                            $prosConsHtml .= "<h3>Advantages</h3>\n<ul>\n";
                            foreach ($articleData['pros_cons']['pros'] as $pro) {
                                $prosConsHtml .= "<li>" . htmlspecialchars($pro) . "</li>\n";
                            }
                            $prosConsHtml .= "</ul>\n";
                        }
                        if (!empty($articleData['pros_cons']['cons'])) {
                            $prosConsHtml .= "<h3>Disadvantages</h3>\n<ul>\n";
                            foreach ($articleData['pros_cons']['cons'] as $con) {
                                $prosConsHtml .= "<li>" . htmlspecialchars($con) . "</li>\n";
                            }
                            $prosConsHtml .= "</ul>\n";
                        }
                        // Insert before conclusion if exists, otherwise append
                        if (stripos($articleData['content'], '<h2>Conclusion') !== false) {
                            $articleData['content'] = str_ireplace('<h2>Conclusion', $prosConsHtml . '<h2>Conclusion', $articleData['content']);
                        } else {
                            $articleData['content'] .= $prosConsHtml;
                        }
                    }
                    
                    // Generate and insert Table of Contents if not already present
                    if (stripos($articleData['content'], 'table-of-contents') === false && 
                        stripos($articleData['content'], 'table of contents') === false) {
                        $tocResult = generateTableOfContents($articleData['content']);
                        if (!empty($tocResult['toc'])) {
                            // Find position after first paragraph/intro (before first H2)
                            $firstH2Pos = stripos($articleData['content'], '<h2');
                            if ($firstH2Pos !== false) {
                                // Check if there's content before first H2 (intro paragraph)
                                $beforeH2 = substr($articleData['content'], 0, $firstH2Pos);
                                if (stripos($beforeH2, '</p>') !== false) {
                                    // Insert TOC after first paragraph closes
                                    $lastPClose = strrpos($beforeH2, '</p>');
                                    $articleData['content'] = substr_replace(
                                        $articleData['content'],
                                        "\n\n" . $tocResult['toc'] . "\n\n",
                                        $lastPClose + 4,
                                        0
                                    );
                                } else {
                                    // Insert before first H2
                                    $articleData['content'] = substr_replace(
                                        $articleData['content'],
                                        $tocResult['toc'] . "\n\n",
                                        $firstH2Pos,
                                        0
                                    );
                                }
                            } else {
                                // No H2 found, try to insert after first paragraph
                                $firstPClose = stripos($articleData['content'], '</p>');
                                if ($firstPClose !== false) {
                                    $articleData['content'] = substr_replace(
                                        $articleData['content'],
                                        "\n\n" . $tocResult['toc'] . "\n\n",
                                        $firstPClose + 4,
                                        0
                                    );
                                }
                            }
                        }
                    } else {
                        // TOC exists, but ensure headings have IDs for anchor links
                        $tocResult = generateTableOfContents($articleData['content']);
                        if (!empty($tocResult['content'])) {
                            $articleData['content'] = $tocResult['content'];
                        }
                    }
                    
                    return [
                        'success' => true,
                        'article' => $articleData
                    ];
                }
            }
        }
        
        return [
            'success' => false,
            'error' => 'Failed to parse AI response. Please try again.'
        ];
    } catch (Exception $e) {
        error_log("AI Article Generation Error: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'Error generating article: ' . $e->getMessage()
        ];
    }
}

/**
 * Add alt text to images that are missing it (for 100% SEO score).
 */
function addAltTextToImages($content, $focusKeyword) {
    $keyword = trim($focusKeyword);
    if ($keyword === '') {
        $keyword = 'technology and software development';
    }
    $content = preg_replace_callback(
        '/<img([^>]*)\s*\/?>/i',
        function ($m) use ($keyword) {
            if (preg_match('/\salt\s*=/i', $m[1])) {
                return $m[0];
            }
            $alt = htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8');
            return '<img' . $m[1] . ' alt="' . $alt . '">';
        },
        $content
    );
    return $content;
}

/**
 * Inject internal links into content to reach 3+ links (for 100% SEO score).
 */
function injectInternalLinksToContent($content, $keywords) {
    $suggestions = suggestInternalLinks($content, $keywords);
    $baseUrl = defined('SITE_BASE_URL') ? SITE_BASE_URL : 'https://techweblabs.com';
    $currentCount = substr_count(strtolower($content), 'href="' . strtolower($baseUrl));
    $injected = 0;
    $maxToInject = max(0, 3 - $currentCount);
    if ($maxToInject <= 0) {
        return $content;
    }
    foreach ($suggestions as $sug) {
        if ($injected >= $maxToInject) {
            break;
        }
        $anchor = $sug['anchor'];
        $url = $sug['url'];
        $fullUrl = rtrim($baseUrl, '/') . '/' . ltrim($url, '/');
        $link = '<a href="' . htmlspecialchars($fullUrl) . '">' . htmlspecialchars($anchor) . '</a>';
        $pattern = '/\b(' . preg_quote($anchor, '/') . ')\b/i';
        if (preg_match($pattern, $content) && stripos($content, $fullUrl) === false) {
            $content = preg_replace($pattern, $link, $content, 1);
            $injected++;
        }
    }
    return $content;
}

/**
 * Generate a blog banner image from title using DALL·E and upload to server.
 * Returns full URL of saved banner or null on failure.
 */
function generateAndUploadBanner($title, $slug) {
    if (!isAIConfigured()) {
        return null;
    }
    $apiKey = getAIAPIKey();
    $slugSafe = preg_replace('/[^a-z0-9\-]/', '-', strtolower($slug));
    $slugSafe = substr($slugSafe, 0, 80);
    $filename = $slugSafe . '-' . time() . '.png';

    $docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : (defined('ROOT_DIR') ? rtrim(ROOT_DIR, '/') : realpath(__DIR__ . '/../..'));
    $saveDir = rtrim($docRoot, '/') . '/assets/blog-banners/';
    if (!is_dir($saveDir)) {
        if (!@mkdir($saveDir, 0755, true)) {
            error_log("Banner upload: could not create directory: " . $saveDir);
            return null;
        }
    }

    $titleShort = trim(substr($title, 0, 80));
    $prompt = "Create a vibrant, friendly ILLUSTRATIVE BANNER for a blog article. Clean cartoonish illustration style, approachable and bright. Wide horizontal layout.

COLORS AND BACKGROUND:
- Soft light blue sky at top with faint clouds; lower area transitions to light green hazy landscape with stylized buildings, trees, and greenery. Small sparkle or star-like details in the upper area. Sense of depth and a clean urban or service environment.

TITLE AND CTA:
- TOP CENTER: Prominent headline. The exact article title must be: \"" . addslashes($titleShort) . "\". Use dark text for most of the title and BRIGHT GREEN for the key phrase or last important word (e.g. brand name or \"Complete Guide\").
- BOTTOM: A rounded rectangular button with soft yellow background and dark text, e.g. \"Read Step-by-Step Tutorial\" or \"Read Guide\" or \"Learn More\".

LEFT SIDE:
- A modern smartphone (teal-green or green bezel) showing an app interface relevant to the article topic (e.g. grocery/delivery: categories like VEGETABLES, FRUITS, ADD buttons; healthcare: health app; generic: app icons). In front of the phone: a paper bag or container with items that match the topic (e.g. fresh produce, groceries, or relevant products). Next to it: golden coins, green banknotes, and a small credit card to suggest payments or value.

CENTER:
- A bright yellow delivery scooter or vehicle (or topic-relevant action element) with a green delivery box or cargo showing a shopping cart or app logo. A location pin or speech bubble with text like \"Delivery 10-20 min\" or similar. A dashed golden or yellow line (delivery route) connecting map pins from left toward the right. In the background: stylized city or town with light green buildings, striped awnings, lush greenery.

RIGHT SIDE:
- A smiling delivery person or professional (e.g. in bright green polo and cap, or topic-appropriate uniform) holding a smartphone in one hand and a brown paper bag or package in the other, with a small logo on the bag. Friendly, approachable pose.

STYLE RULES:
- Illustrated, flat to semi-flat. NOT photorealistic. Friendly, bright, slightly cartoonish. Color palette: greens, yellows, earthy tones, light blue, brown for bags. All text in English and readable. Reflect the article topic in the visuals (e.g. grocery/delivery: app with categories, grocery bags, scooter; healthcare: health app, medical items; app development: app UI, developer). Include the exact article title at the top and the yellow CTA button at the bottom.";
    $url = 'https://api.openai.com/v1/images/generations';
    $data = [
        'model' => 'dall-e-3',
        'prompt' => $prompt,
        'n' => 1,
        'size' => '1792x1024',
        'quality' => 'hd',
        'response_format' => 'url',
        'style' => 'natural'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        $err = json_decode($response, true);
        error_log("DALL·E API error: " . ($err['error']['message'] ?? $response));
        return null;
    }

    $result = json_decode($response, true);
    $imageUrl = $result['data'][0]['url'] ?? null;
    if (!$imageUrl) {
        return null;
    }

    $imageData = @file_get_contents($imageUrl);
    if ($imageData === false) {
        error_log("Banner: failed to download image from OpenAI");
        return null;
    }

    $filepath = $saveDir . $filename;
    if (file_put_contents($filepath, $imageData) === false) {
        error_log("Banner: failed to save file: " . $filepath);
        return null;
    }

    $baseUrl = defined('SITE_BASE_URL') ? SITE_BASE_URL : 'https://techweblabs.com';
    return rtrim($baseUrl, '/') . '/assets/blog-banners/' . $filename;
}

/**
 * Fix SEO issues in an existing article using AI. Targets 100% score: meta, content (alt, internal links), and banner.
 */
function fixArticleSEOIssues($articleData) {
    if (!isAIConfigured()) {
        return [
            'success' => false,
            'error' => 'AI is not configured. Please set up OpenAI API key.'
        ];
    }

    $seoScore = calculateSEOScore($articleData);
    $issues = $seoScore['issues'] ?? [];
    $percentage = $seoScore['percentage'] ?? 0;
    $focusKeyword = $articleData['seo_focus_keyword'] ?? '';
    $keywordsStr = is_array($articleData['meta_keywords'] ?? null) ? implode(', ', $articleData['meta_keywords']) : ($articleData['meta_keywords'] ?? '');

    $prompt = "You are an SEO expert. Fix the article meta so it scores 100% on SEO. Return valid JSON only.

STRICT RULES (must be followed exactly):
- title: EXACTLY 50 to 60 characters (count carefully). Must include the focus keyword.
- meta_description: EXACTLY 150 to 160 characters. Must include focus keyword and a compelling call-to-action.
- meta_keywords: Comma-separated list including focus keyword and 4-5 related terms.
- seo_focus_keyword: Keep or refine the primary focus keyword (2-4 words).
- excerpt: 2-3 sentences summarizing the article, include focus keyword. No length limit for excerpt.

Current values:
Title: " . json_encode($articleData['title'] ?? '') . "
Meta description: " . json_encode($articleData['meta_description'] ?? '') . "
Meta keywords: " . json_encode($articleData['meta_keywords'] ?? '') . "
Focus keyword: " . json_encode($focusKeyword) . "
Excerpt: " . json_encode(mb_substr($articleData['excerpt'] ?? '', 0, 600)) . "

Return ONLY this JSON (no markdown, no explanation):
{\"title\": \"...\", \"meta_description\": \"...\", \"meta_keywords\": \"...\", \"seo_focus_keyword\": \"...\", \"excerpt\": \"...\"}";

    try {
        $response = callOpenAI($prompt, 1000);
        if (!$response || !isset($response['choices'][0]['message']['content'])) {
            return ['success' => false, 'error' => 'Failed to get AI response.'];
        }

        $content = $response['choices'][0]['message']['content'];
        $jsonStart = strpos($content, '{');
        $jsonEnd = strrpos($content, '}') + 1;
        if ($jsonStart === false || $jsonEnd === false) {
            return ['success' => false, 'error' => 'Invalid AI response format.'];
        }
        $fixed = json_decode(substr($content, $jsonStart, $jsonEnd - $jsonStart), true);
        if (!$fixed) {
            return ['success' => false, 'error' => 'Could not parse fixed article.'];
        }

        $title = trim($fixed['title'] ?? $articleData['title'] ?? '');
        $metaDesc = trim($fixed['meta_description'] ?? $articleData['meta_description'] ?? '');
        if (mb_strlen($title) > 60) {
            $title = mb_substr($title, 0, 57) . '...';
        }
        if (mb_strlen($metaDesc) > 160) {
            $metaDesc = mb_substr($metaDesc, 0, 157) . '...';
        }

        $articleData['title'] = $title;
        $articleData['meta_description'] = $metaDesc;
        $articleData['meta_keywords'] = $fixed['meta_keywords'] ?? $articleData['meta_keywords'];
        $articleData['seo_focus_keyword'] = trim($fixed['seo_focus_keyword'] ?? $focusKeyword);
        $articleData['excerpt'] = trim($fixed['excerpt'] ?? $articleData['excerpt'] ?? '');

        $articleData['content'] = sanitizeArticleImages($articleData['content'] ?? '', getAllowedImageUrls());
        $articleData['content'] = addAltTextToImages($articleData['content'], $articleData['seo_focus_keyword']);
        $articleData['content'] = injectInternalLinksToContent($articleData['content'], $keywordsStr . ',' . $articleData['seo_focus_keyword']);

        $slug = $articleData['slug'] ?? '';
        $bannerUrl = generateAndUploadBanner($articleData['title'], $slug ?: 'blog-' . time());
        if ($bannerUrl) {
            $articleData['featured_image'] = $bannerUrl;
            $articleData['og_image'] = $bannerUrl;
        }

        $articleData['seo_score'] = calculateSEOScore($articleData);

        return [
            'success' => true,
            'article' => $articleData,
            'message' => 'SEO optimized. New score: ' . ($articleData['seo_score']['percentage'] ?? 0) . '%' . ($bannerUrl ? '. Banner generated and set as thumbnail.' : '')
        ];
    } catch (Exception $e) {
        error_log("Fix SEO Issues Error: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'Error fixing SEO: ' . $e->getMessage()
        ];
    }
}

/**
 * Call OpenAI API
 */
function callOpenAI($prompt, $maxTokens = 2000) {
    $apiKey = getAIAPIKey();
    if (!$apiKey) {
        throw new Exception('OpenAI API key not configured');
    }
    
    $url = 'https://api.openai.com/v1/chat/completions';
    
    $data = [
        'model' => getAIModel(),
        'messages' => [
            [
                'role' => 'system',
                'content' => 'You are an expert SEO content writer specializing in technology and software development topics. Always return valid JSON format.'
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ],
        'max_tokens' => $maxTokens,
        'temperature' => 0.7
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        $errorData = json_decode($response, true);
        throw new Exception('OpenAI API error: ' . ($errorData['error']['message'] ?? 'Unknown error'));
    }
    
    return json_decode($response, true);
}

/**
 * Publish AI-generated article to database
 */
function publishAIGeneratedArticle($articleData, $authorId) {
    try {
        if (!function_exists('getDB')) {
            require_once __DIR__ . '/../config/db_config.php';
        }
        
        $db = getDB();
        
        // Check if slug exists
        $existing = $db->prepare("SELECT id FROM blog_posts WHERE slug = ?");
        $existing->execute([$articleData['slug']]);
        
        if ($existing->fetch()) {
            // Append timestamp if slug exists
            $articleData['slug'] = $articleData['slug'] . '-' . time();
        }
        
        $featuredImage = $articleData['featured_image'] ?? $articleData['og_image'] ?? '';
        $ogImage = $articleData['og_image'] ?? $articleData['featured_image'] ?? '';

        // Insert article (including thumbnail/banner URL from Fix SEO or generation)
        $sql = "INSERT INTO blog_posts (
            slug, title, meta_title, meta_description, meta_keywords,
            seo_focus_keyword, excerpt, content, featured_image, og_image, author_id, status,
            reading_time, published_at, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), NOW())";
        
        $stmt = $db->prepare($sql);
        $result = $stmt->execute([
            $articleData['slug'],
            $articleData['title'],
            $articleData['title'], // meta_title
            $articleData['meta_description'],
            $articleData['meta_keywords'],
            $articleData['seo_focus_keyword'],
            $articleData['excerpt'],
            $articleData['content'],
            $featuredImage,
            $ogImage,
            $authorId,
            $articleData['status'] ?? 'published',
            $articleData['reading_time'] ?? 5
        ]);
        
        if ($result) {
            $postId = $db->lastInsertId();
            
            // Optionally assign to default category
            // You can enhance this to allow category selection
            
            return [
                'success' => true,
                'post_id' => $postId
            ];
        } else {
            return [
                'success' => false,
                'error' => 'Failed to save article to database'
            ];
        }
    } catch (Exception $e) {
        error_log("Publish Article Error: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'Error: ' . $e->getMessage()
        ];
    }
}

/**
 * Fallback topics if AI is not available - More diverse and service-specific
 */
function getFallbackTopics() {
    // Shuffle and return different topics each time
    $allTopics = [
        [
            'title' => 'Complete Food Delivery App Development Process: Step-by-Step Guide 2026',
            'keywords' => ['food delivery app development', 'food delivery app development process', 'steps to build food delivery app', 'restaurant app development', 'food ordering app'],
            'reason' => 'High search volume for food delivery app development process. Targets restaurants and food businesses looking to digitize.',
            'trending' => true,
            'category' => 'Food Delivery App Development',
            'angle' => 'Process/Steps'
        ],
        [
            'title' => 'How to Build a Grocery Delivery App Like BigBasket: Complete Guide',
            'keywords' => ['grocery delivery app development', 'bigbasket clone app', 'grocery app features', 'online grocery app development'],
            'reason' => 'Popular business model with high search demand. Targets grocery businesses and entrepreneurs.',
            'trending' => true,
            'category' => 'Grocery App Development',
            'angle' => 'How-to Guide'
        ],
        [
            'title' => 'Food Delivery App Development Cost in India: Complete Breakdown 2026',
            'keywords' => ['food delivery app development cost', 'app development price', 'food app development budget', 'cost to develop food delivery app'],
            'reason' => 'Critical information for businesses planning food delivery apps. High commercial intent searches.',
            'trending' => true,
            'category' => 'Food Delivery App Development',
            'angle' => 'Cost/Budget'
        ],
        [
            'title' => 'Essential Features for Healthcare Mobile App Development',
            'keywords' => ['healthcare app features', 'medical app development', 'healthcare mobile app', 'health app development'],
            'reason' => 'Growing healthcare digitization trend. High-value leads for healthcare app development.',
            'trending' => true,
            'category' => 'Healthcare App Development',
            'angle' => 'Feature Lists'
        ],
        [
            'title' => 'React Native vs Flutter: Which is Better for Startup App Development?',
            'keywords' => ['react native vs flutter', 'cross-platform development', 'flutter vs react native', 'best mobile framework'],
            'reason' => 'Evergreen comparison topic with consistent high search volume and decision-making intent.',
            'trending' => true,
            'category' => 'Cross-Platform Development',
            'angle' => 'Technology Comparison'
        ],
        [
            'title' => 'Step-by-Step Guide to Taxi App Development Like Uber',
            'keywords' => ['taxi app development', 'uber clone app', 'ride sharing app development', 'taxi booking app features'],
            'reason' => 'Popular on-demand service with high search volume. Targets transportation businesses.',
            'trending' => true,
            'category' => 'Taxi App Development',
            'angle' => 'Step-by-Step Guide'
        ],
        [
            'title' => 'Why TechWebLabs is the Best Mobile App Development Company in Hyderabad',
            'keywords' => ['best app development company hyderabad', 'mobile app developers hyderabad', 'techweblabs', 'app development company'],
            'reason' => 'Brand-focused content targeting local searches. Helps establish authority and trust.',
            'trending' => false,
            'category' => 'Company Showcase',
            'angle' => 'Company Showcase'
        ],
        [
            'title' => 'Top 10 Mobile App Development Trends Shaping 2026',
            'keywords' => ['mobile app development trends 2026', 'app development trends', 'mobile technology trends', 'future of app development'],
            'reason' => 'High search volume for trending technologies. Positions as industry thought leader.',
            'trending' => true,
            'category' => 'Mobile App Development',
            'angle' => 'Trends & Future'
        ],
        [
            'title' => 'E-commerce App Development: Complete Features Checklist for 2026',
            'keywords' => ['ecommerce app development', 'ecommerce app features', 'online store app', 'shopping app development'],
            'reason' => 'Growing e-commerce market. Targets businesses wanting to expand online presence.',
            'trending' => true,
            'category' => 'E-commerce Development',
            'angle' => 'Feature Lists'
        ],
        [
            'title' => 'MVP Development Process: How to Build Your First Mobile App MVP',
            'keywords' => ['mvp development', 'minimum viable product', 'mvp app development', 'startup mvp development'],
            'reason' => 'Essential for startups. Targets entrepreneurs looking to validate ideas quickly.',
            'trending' => true,
            'category' => 'MVP Development',
            'angle' => 'Process/Steps'
        ],
        [
            'title' => 'iOS App Development Cost in India: Complete Pricing Guide 2026',
            'keywords' => ['ios app development cost', 'iphone app development price', 'ios app development budget', 'swift development cost'],
            'reason' => 'High commercial intent. Targets businesses planning iOS app development.',
            'trending' => false,
            'category' => 'iOS App Development',
            'angle' => 'Cost/Budget'
        ],
        [
            'title' => 'Best Practices for On-Demand Service App Development',
            'keywords' => ['on-demand app development', 'on-demand service app', 'on-demand app features', 'uber clone development'],
            'reason' => 'Covers multiple on-demand services. High-value topic for service-based businesses.',
            'trending' => true,
            'category' => 'On-Demand Services',
            'angle' => 'Best Practices'
        ]
    ];
    
    // Shuffle and return 6-8 random topics for variety
    shuffle($allTopics);
    return array_slice($allTopics, 0, 8);
}

/**
 * Helper functions
 * Note: generateSlug() is already defined in auth.php, so we just use it directly
 */

function calculateReadingTime($wordCount) {
    // Average reading speed: 200-250 words per minute
    $wordsPerMinute = 200;
    $minutes = max(1, ceil($wordCount / $wordsPerMinute));
    return $minutes;
}
