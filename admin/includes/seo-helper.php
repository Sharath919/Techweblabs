<?php
/**
 * SEO Helper Functions for Article Generation
 */

/**
 * Get internal linking opportunities for TechWebLabs
 * This function is used by both seo-helper.php and ai-helper.php
 */
function getInternalLinkingOpportunities() {
    return [
        'services' => [
            'Mobile App Development' => [
                'url' => 'custom-app-development',
                'keywords' => ['custom app development', 'mobile app development', 'app development services']
            ],
            'iOS App Development' => [
                'url' => 'ios-app-development',
                'keywords' => ['ios app development', 'iphone app development', 'swift development']
            ],
            'Android App Development' => [
                'url' => 'android-app-development',
                'keywords' => ['android app development', 'android development', 'kotlin development']
            ],
            'Cross-Platform App Development' => [
                'url' => 'cross-platform-app',
                'keywords' => ['cross-platform development', 'react native', 'flutter development']
            ],
            'Food Delivery App Development' => [
                'url' => 'food-delivery-app-development',
                'keywords' => ['food delivery app', 'food ordering app', 'restaurant app development']
            ],
            'Grocery Delivery App Development' => [
                'url' => 'grocery-app-development',
                'keywords' => ['grocery delivery app', 'grocery app development', 'online grocery app']
            ],
            'Taxi App Development' => [
                'url' => 'on-demand-taxi-booking-app-development',
                'keywords' => ['taxi app development', 'ride sharing app', 'uber clone app']
            ],
            'Healthcare App Development' => [
                'url' => 'healthcare-mobile-app-development',
                'keywords' => ['healthcare app development', 'medical app development', 'health app']
            ],
            'E-commerce Website Development' => [
                'url' => 'e-commerce-website',
                'keywords' => ['ecommerce website', 'online store development', 'e-commerce platform']
            ],
            'Custom Website Development' => [
                'url' => 'custom-website-development',
                'keywords' => ['custom website development', 'website development', 'web development services']
            ],
            'UI/UX Design Services' => [
                'url' => 'visual-design-services',
                'keywords' => ['ui ux design', 'app design', 'website design', 'user interface design']
            ],
            'App Store Optimization' => [
                'url' => 'app-store-optimization',
                'keywords' => ['aso', 'app store optimization', 'app store ranking']
            ]
        ],
        'company_info' => [
            'About Us' => [
                'url' => 'about',
                'keywords' => ['techweblabs', 'app development company', 'web development company']
            ],
            'Contact Us' => [
                'url' => 'contact',
                'keywords' => ['contact techweblabs', 'get in touch', 'hire developers']
            ],
            'Portfolio' => [
                'url' => 'portfolio',
                'keywords' => ['our work', 'case studies', 'projects']
            ]
        ],
        'cities' => [
            'Hyderabad' => [
                'keywords' => ['app development hyderabad', 'mobile app developers hyderabad', 'web development hyderabad']
            ]
        ]
    ];
}

/**
 * Calculate SEO score for an article
 */
function calculateSEOScore($articleData) {
    $score = 0;
    $maxScore = 110; // Increased to 110 to account for TOC and images
    $issues = [];
    $recommendations = [];
    
    // Title optimization (15 points)
    $title = $articleData['title'] ?? '';
    $titleLength = strlen($title);
    if ($titleLength >= 30 && $titleLength <= 60) {
        $score += 15;
    } elseif ($titleLength > 60 && $titleLength <= 70) {
        $score += 10;
        $issues[] = 'Title is slightly long (recommended 50-60 characters)';
    } else {
        $issues[] = 'Title length should be 50-60 characters for optimal SEO';
        $recommendations[] = 'Optimize title length to 50-60 characters';
    }
    
    // Meta description (10 points)
    $metaDesc = $articleData['meta_description'] ?? '';
    $metaLength = strlen($metaDesc);
    if ($metaLength >= 120 && $metaLength <= 160) {
        $score += 10;
    } elseif ($metaLength > 160 && $metaLength <= 180) {
        $score += 7;
        $issues[] = 'Meta description is slightly long';
    } else {
        $issues[] = 'Meta description should be 150-160 characters';
        $recommendations[] = 'Optimize meta description to 150-160 characters';
    }
    
    // Focus keyword in title (10 points)
    $focusKeyword = strtolower($articleData['seo_focus_keyword'] ?? '');
    if (!empty($focusKeyword) && stripos($title, $focusKeyword) !== false) {
        $score += 10;
    } else {
        $issues[] = 'Focus keyword not found in title';
        $recommendations[] = 'Include focus keyword in the title';
    }
    
    // Focus keyword in meta description (5 points)
    if (!empty($focusKeyword) && stripos($metaDesc, $focusKeyword) !== false) {
        $score += 5;
    } else {
        $recommendations[] = 'Include focus keyword in meta description';
    }
    
    // Content length (15 points)
    $content = strip_tags($articleData['content'] ?? '');
    $wordCount = str_word_count($content);
    if ($wordCount >= 1000 && $wordCount <= 2500) {
        $score += 15;
    } elseif ($wordCount >= 800 && $wordCount < 1000) {
        $score += 10;
        $issues[] = 'Content could be longer for better SEO (aim for 1000+ words)';
    } elseif ($wordCount >= 2500) {
        $score += 12;
        $issues[] = 'Content is very long (consider breaking into multiple articles)';
    } else {
        $issues[] = 'Content is too short (minimum 800 words recommended)';
        $recommendations[] = 'Expand content to at least 1000 words';
    }
    
    // Headings structure (10 points)
    $hasH2 = preg_match('/<h2[^>]*>/i', $articleData['content'] ?? '');
    $hasH3 = preg_match('/<h3[^>]*>/i', $articleData['content'] ?? '');
    if ($hasH2) {
        $score += 7;
    } else {
        $issues[] = 'No H2 headings found';
        $recommendations[] = 'Add H2 headings to structure content';
    }
    if ($hasH3) {
        $score += 3;
    }
    
    // Internal links (15 points)
    $internalLinks = substr_count(strtolower($articleData['content'] ?? ''), 'href="https://techweblabs.com/');
    if ($internalLinks >= 3) {
        $score += 15;
    } elseif ($internalLinks >= 2) {
        $score += 10;
        $recommendations[] = 'Add more internal links (3-5 recommended)';
    } elseif ($internalLinks >= 1) {
        $score += 5;
        $issues[] = 'Not enough internal links';
        $recommendations[] = 'Add 3-5 internal links to related service pages';
    } else {
        $issues[] = 'No internal links found';
        $recommendations[] = 'Add internal links to improve SEO';
    }
    
    // Images with alt text (5 points)
    $hasImages = preg_match('/<img[^>]*>/i', $articleData['content'] ?? '');
    $hasAltText = preg_match('/<img[^>]*alt=["\'][^"\']+["\']/i', $articleData['content'] ?? '');
    if ($hasImages && $hasAltText) {
        $score += 5;
    } elseif ($hasImages) {
        $issues[] = 'Images found but missing alt text';
        $recommendations[] = 'Add alt text to all images';
    }
    
    // FAQs presence (10 points)
    if (isset($articleData['faqs']) && is_array($articleData['faqs']) && count($articleData['faqs']) >= 3) {
        $score += 10;
    } else {
        $recommendations[] = 'Add FAQ section (3-5 questions recommended)';
    }
    
    // Keyword density (5 points)
    if (!empty($content) && !empty($focusKeyword)) {
        $keywordCount = substr_count(strtolower($content), strtolower($focusKeyword));
        $keywordDensity = ($keywordCount / max($wordCount, 1)) * 100;
        if ($keywordDensity >= 0.5 && $keywordDensity <= 2.5) {
            $score += 5;
        } elseif ($keywordDensity < 0.5) {
            $recommendations[] = 'Increase focus keyword usage naturally in content';
        } else {
            $issues[] = 'Keyword density too high (risk of keyword stuffing)';
            $recommendations[] = 'Reduce keyword usage to maintain natural flow';
        }
    }
    
    // Pros/Cons or structured content (5 points)
    if (isset($articleData['pros_cons']) || 
        stripos($content, 'pros') !== false || 
        stripos($content, 'advantages') !== false ||
        stripos($content, 'benefits') !== false) {
        $score += 5;
    } else {
        $recommendations[] = 'Consider adding pros/cons or benefits section';
    }
    
    // TechWebLabs promotion (5 points)
    if (stripos($content, 'techweblabs') !== false || stripos($content, 'techweblabs') !== false) {
        $score += 5;
    } else {
        $recommendations[] = 'Add natural mention of TechWebLabs in content';
    }
    
    // Table of Contents (5 points)
    if (stripos($content, 'table-of-contents') !== false || 
        stripos($content, 'table of contents') !== false ||
        preg_match('/<div[^>]*class=["\'][^"\']*table-of-contents/i', $content)) {
        $score += 5;
    } else {
        $recommendations[] = 'Add table of contents for better navigation and SEO';
    }
    
    // Images count (5 points - bonus if images present)
    $imageCount = preg_match_all('/<img[^>]*>/i', $articleData['content'] ?? '');
    if ($imageCount >= 2) {
        $score += 5;
    } elseif ($imageCount == 1) {
        $score += 3;
        $recommendations[] = 'Add more images (2-3 recommended)';
    } else {
        $recommendations[] = 'Add relevant images to make content more engaging';
    }
    
    // Determine grade
    $grade = 'F';
    $gradeColor = '#ef4444';
    if ($score >= 90) {
        $grade = 'A+';
        $gradeColor = '#10b981';
    } elseif ($score >= 80) {
        $grade = 'A';
        $gradeColor = '#10b981';
    } elseif ($score >= 70) {
        $grade = 'B';
        $gradeColor = '#3b82f6';
    } elseif ($score >= 60) {
        $grade = 'C';
        $gradeColor = '#f59e0b';
    } elseif ($score >= 50) {
        $grade = 'D';
        $gradeColor = '#f97316';
    }
    
    return [
        'score' => $score,
        'max_score' => $maxScore,
        'percentage' => round(($score / $maxScore) * 100),
        'grade' => $grade,
        'grade_color' => $gradeColor,
        'issues' => $issues,
        'recommendations' => $recommendations,
        'word_count' => $wordCount,
        'internal_links' => $internalLinks,
        'keyword_density' => isset($keywordDensity) ? round($keywordDensity, 2) : 0
    ];
}

/**
 * Get the full list of ALLOWED image URLs only (files that exist on site).
 * AI and article generation must use ONLY these URLs - no inventing names.
 */
function getAllowedImageUrls() {
    $base = 'https://techweblabs.com/images/';
    return [
        $base . 'deliveroo/deliveroo-1.png',
        $base . 'deliveroo/deliveroo-2.png',
        $base . 'deliveroo/deliveroo-3.png',
        $base . 'deliveroo/deliveroo-4.png',
        $base . 'deliveroo/deliveroo-5.png',
        $base . 'deliveroo/deliveroo-6.png',
        $base . 'deliveroo/deliveroo-7.png',
        $base . 'deliveroo/deliveroo-8.png',
        $base . 'grocery/grocery-1.png',
        $base . 'grocery/grocery-2.png',
        $base . 'grocery/grocery-3.png',
        $base . 'grocery/grocery-4.png',
        $base . 'taxigo/taxigo-1.png',
        $base . 'taxigo/taxigo-2.png',
        $base . 'taxigo/features1.png',
        $base . 'taxigo/features2.png',
        $base . 'taxigo/features3.png',
        $base . 'uber/uber-home.png',
        $base . 'uber/uber-features.png',
        $base . 'uber/uber-rider.png',
        $base . 'uber/uber-user.png',
        $base . 'mobile-app/mobile-app-1.png',
        $base . 'mobile-app/mobile-app-2.png',
        $base . 'mobile-app/mobile-app.png',
        $base . 'ios-app/ios-app-1.png',
        $base . 'ios-app/ios-app.png',
        $base . 'cross-platform/cross-platform-1.png',
        $base . 'cross-platform/cross-platform.png',
        $base . 'icons/mobile/ios.png',
        $base . 'icons/mobile/android.png',
        $base . 'icons/mobile/react.png',
        $base . 'icons/mobile/flutter.png',
        $base . 'e-commerce/ecommerce-1.png',
        $base . 'e-commerce/ecommerce-website.png',
        $base . 'e-commerce/ecommerce-features.png',
        $base . 'app-development.png',
        $base . 'hero-mobile_hero_banner.png',
        $base . 'service-app-develops.png',
        $base . 'Zomato/zomato-vendor1.png',
        $base . 'Zomato/featurs.png',
        $base . 'ubereats/ubereats-1.png',
        $base . 'ubereats/ubereats-2.png',
        $base . 'realestate/Real-1.png',
        $base . 'realestate/real-4.png',
        $base . 'Mvp/mvp-1.png',
        $base . 'Mvp/mvp-2.png',
        $base . 'website-maintenance/website-maintenance-1.png',
        $base . 'logo-black.webp',
    ];
}

/**
 * Sanitize article content: replace any img src not in allowed list with an allowed URL.
 * Ensures no 404 image links from AI inventing filenames.
 */
function sanitizeArticleImages($content, array $allowedUrls = null) {
    if ($allowedUrls === null) {
        $allowedUrls = getAllowedImageUrls();
    }
    $allowedSet = array_flip(array_map('strtolower', $allowedUrls));
    $replacementIndex = 0;
    $count = count($allowedUrls);

    $content = preg_replace_callback(
        '/<img([^>]*)\ssrc=(["\'])([^"\']+)\2([^>]*)>/i',
        function ($m) use ($allowedUrls, $allowedSet, &$replacementIndex, $count) {
            $url = trim($m[3]);
            $urlNormalized = rtrim($url, '/');
            $isAllowed = isset($allowedSet[strtolower($url)]) || isset($allowedSet[strtolower($urlNormalized)]);
            if (!$isAllowed) {
                $replacementUrl = $allowedUrls[$replacementIndex % $count];
                $replacementIndex++;
                return '<img' . $m[1] . ' src=' . $m[2] . $replacementUrl . $m[2] . $m[4] . '>';
            }
            return $m[0];
        },
        $content
    );

    return $content;
}

/**
 * Get relevant images based on topic/keywords.
 * Returns ONLY URLs from getAllowedImageUrls() - no custom/invented paths.
 */
function getRelevantImages($topic, $keywords) {
    $topicLower = strtolower($topic);
    $keywordsArray = array_map('trim', explode(',', strtolower($keywords)));
    $allowed = getAllowedImageUrls();

    // Topic/keyword to allowed image path suffixes (must match getAllowedImageUrls)
    $imageMap = [
        'food delivery' => ['deliveroo/deliveroo-1.png', 'deliveroo/deliveroo-5.png', 'deliveroo/deliveroo-8.png'],
        'grocery' => ['grocery/grocery-1.png', 'grocery/grocery-2.png', 'grocery/grocery-3.png'],
        'taxi' => ['taxigo/taxigo-1.png', 'taxigo/taxigo-2.png', 'taxigo/features1.png'],
        'ride' => ['uber/uber-home.png', 'uber/uber-rider.png', 'taxigo/taxigo-1.png'],
        'mobile app' => ['mobile-app/mobile-app-1.png', 'mobile-app/mobile-app.png', 'app-development.png'],
        'ios' => ['ios-app/ios-app-1.png', 'icons/mobile/ios.png', 'app-development.png'],
        'android' => ['icons/mobile/android.png', 'mobile-app/mobile-app-1.png', 'app-development.png'],
        'react native' => ['icons/mobile/react.png', 'cross-platform/cross-platform.png', 'app-development.png'],
        'flutter' => ['icons/mobile/flutter.png', 'cross-platform/cross-platform-1.png', 'app-development.png'],
        'healthcare' => ['service-app-develops.png', 'mobile-app/mobile-app-1.png', 'app-development.png'],
        'ecommerce' => ['e-commerce/ecommerce-1.png', 'e-commerce/ecommerce-website.png', 'app-development.png'],
        'default' => ['app-development.png', 'mobile-app/mobile-app-1.png', 'hero-mobile_hero_banner.png'],
    ];

    $base = 'https://techweblabs.com/images/';
    $matchedImages = [];

    foreach ($imageMap as $key => $paths) {
        if ($key === 'default') {
            continue;
        }
        if (stripos($topicLower, $key) !== false) {
            foreach ($paths as $p) {
                $url = $base . $p;
                if (in_array($url, $allowed, true)) {
                    $matchedImages[] = $url;
                }
            }
            break;
        }
    }

    if (empty($matchedImages)) {
        foreach ($keywordsArray as $keyword) {
            foreach ($imageMap as $key => $paths) {
                if ($key === 'default') {
                    continue;
                }
                if (stripos($keyword, $key) !== false) {
                    foreach ($paths as $p) {
                        $url = $base . $p;
                        if (in_array($url, $allowed, true)) {
                            $matchedImages[] = $url;
                        }
                    }
                    break 2;
                }
            }
        }
    }

    if (empty($matchedImages)) {
        foreach ($imageMap['default'] as $p) {
            $url = $base . $p;
            if (in_array($url, $allowed, true)) {
                $matchedImages[] = $url;
            }
        }
    }

    return array_unique(array_slice($matchedImages, 0, 3));
}

/**
 * Generate Table of Contents from HTML content
 */
function generateTableOfContents($content) {
    // Extract H2 and H3 headings
    preg_match_all('/<h([23])[^>]*>(.*?)<\/h[23]>/i', $content, $matches, PREG_SET_ORDER);
    
    if (empty($matches)) {
        return '';
    }
    
    $toc = '<div class="table-of-contents" style="background: #f9fafb; border: 1px solid #e5e7eb; border-left: 4px solid #667eea; border-radius: 8px; padding: 1.5rem; margin: 2rem 0;"><h3 style="margin: 0 0 1rem 0; color: #1f2937;">📑 Table of Contents</h3><ul style="list-style: none; padding: 0; margin: 0;">';
    
    $index = 1;
    foreach ($matches as $match) {
        $level = (int)$match[1]; // 2 or 3
        $headingText = strip_tags($match[2]);
        $anchor = 'toc-' . preg_replace('/[^a-z0-9]+/', '-', strtolower($headingText));
        
        // Add anchor to heading in content
        $headingWithAnchor = '<h' . $level . ' id="' . $anchor . '">' . $match[2] . '</h' . $level . '>';
        $content = str_replace($match[0], $headingWithAnchor, $content);
        
        $indent = $level === 3 ? ' style="padding-left: 1.5rem;"' : '';
        $toc .= '<li' . $indent . '><a href="#' . $anchor . '" style="color: #667eea; text-decoration: none; display: block; padding: 0.5rem 0; transition: color 0.2s;" onmouseover="this.style.color=\'#764ba2\'" onmouseout="this.style.color=\'#667eea\'">' . $index . '. ' . htmlspecialchars($headingText) . '</a></li>';
        $index++;
    }
    
    $toc .= '</ul></div>';
    
    return ['toc' => $toc, 'content' => $content];
}

/**
 * Suggest internal links based on content
 */
function suggestInternalLinks($content, $keywords) {
    $opportunities = getInternalLinkingOpportunities();
    $suggestions = [];
    $contentLower = strtolower($content);
    $keywordsLower = array_map('strtolower', explode(',', $keywords));
    
    foreach ($opportunities['services'] as $serviceName => $serviceData) {
        foreach ($serviceData['keywords'] as $keyword) {
            if (stripos($contentLower, $keyword) !== false || 
                in_array(strtolower($keyword), $keywordsLower)) {
                $suggestions[] = [
                    'text' => $serviceName,
                    'url' => $serviceData['url'],
                    'anchor' => $keyword,
                    'type' => 'service'
                ];
                break; // Only add once per service
            }
        }
    }
    
    // Always suggest company info links
    $suggestions[] = [
        'text' => 'TechWebLabs',
        'url' => 'about',
        'anchor' => 'app development company',
        'type' => 'company'
    ];
    
    return array_slice($suggestions, 0, 5); // Limit to 5 suggestions
}
