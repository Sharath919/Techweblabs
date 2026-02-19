<?php
/**
 * Schema.org JSON-LD Generator for TechWebLabs
 * Optimized for Google Search and AI Overviews
 */

if (!defined('ROOT_DIR')) {
    define('ROOT_DIR', $_SERVER['DOCUMENT_ROOT'] . '/');
}

/**
 * Get Organization Schema
 */
function getOrganizationSchema() {
    return [
        "@context" => "https://schema.org",
        "@type" => "Organization",
        "name" => "TechWebLabs",
        "alternateName" => "Techweblabs",
        "url" => "https://techweblabs.com",
        "logo" => "https://techweblabs.com/images/logo.png",
        "description" => "TechWebLabs is a leading mobile app and web development company in Hyderabad, India. We provide custom app development, web solutions, AI software development, and digital transformation services for startups and enterprises worldwide.",
        "foundingDate" => "2015",
        "address" => [
            "@type" => "PostalAddress",
            "streetAddress" => "Flat no 102, Plot no 1208, Spline Arcade, Ayyappa Society",
            "addressLocality" => "Madhapur",
            "addressRegion" => "Hyderabad",
            "postalCode" => "500081",
            "addressCountry" => "IN"
        ],
        "contactPoint" => [
            [
                "@type" => "ContactPoint",
                "telephone" => "+91-7670837961",
                "contactType" => "customer service",
                "areaServed" => "Worldwide",
                "availableLanguage" => ["English", "Hindi"]
            ],
            [
                "@type" => "ContactPoint",
                "email" => "info@techweblabs.com",
                "contactType" => "customer service",
                "areaServed" => "Worldwide"
            ]
        ],
        "sameAs" => [
            "https://www.linkedin.com/company/techweblabs",
            "https://twitter.com/techweblabs",
            "https://www.facebook.com/techweblabs",
            "https://www.instagram.com/techweblabs_webdevelopment/",
            "https://in.pinterest.com/techweblabs/",
            "https://www.behance.net/techweblabs1"
        ],
        "areaServed" => [
            "@type" => "Country",
            "name" => "Worldwide"
        ],
        "knowsAbout" => [
            "Mobile App Development",
            "Web Development",
            "AI Software Development",
            "Flutter Development",
            "React Native Development",
            "Custom Software Solutions",
            "MVP Development",
            "UI/UX Design",
            "Digital Transformation"
        ],
        "hasOfferCatalog" => [
            "@type" => "OfferCatalog",
            "name" => "TechWebLabs Services",
            "itemListElement" => [
                [
                    "@type" => "Offer",
                    "itemOffered" => [
                        "@type" => "Service",
                        "name" => "Mobile App Development",
                        "description" => "Custom mobile app development for iOS and Android"
                    ]
                ],
                [
                    "@type" => "Offer",
                    "itemOffered" => [
                        "@type" => "Service",
                        "name" => "Web Development",
                        "description" => "Custom web applications and websites"
                    ]
                ],
                [
                    "@type" => "Offer",
                    "itemOffered" => [
                        "@type" => "Service",
                        "name" => "AI Software Development",
                        "description" => "AI-powered software solutions and machine learning applications"
                    ]
                ]
            ]
        ]
    ];
}

/**
 * Get WebSite Schema with SearchAction
 */
function getWebSiteSchema() {
    return [
        "@context" => "https://schema.org",
        "@type" => "WebSite",
        "name" => "TechWebLabs",
        "url" => "https://techweblabs.com",
        "potentialAction" => [
            "@type" => "SearchAction",
            "target" => [
                "@type" => "EntryPoint",
                "urlTemplate" => "https://techweblabs.com/?s={search_term_string}"
            ],
            "query-input" => "required name=search_term_string"
        ],
        "publisher" => [
            "@type" => "Organization",
            "name" => "TechWebLabs"
        ]
    ];
}

/**
 * Get Service Schema
 */
function getServiceSchema($serviceName, $serviceDescription, $serviceUrl) {
    return [
        "@context" => "https://schema.org",
        "@type" => "Service",
        "serviceType" => $serviceName,
        "provider" => [
            "@type" => "Organization",
            "name" => "TechWebLabs",
            "url" => "https://techweblabs.com"
        ],
        "description" => $serviceDescription,
        "url" => $serviceUrl,
        "areaServed" => [
            "@type" => "Country",
            "name" => "Worldwide"
        ],
        "offers" => [
            "@type" => "Offer",
            "priceCurrency" => "INR",
            "availability" => "https://schema.org/InStock",
            "priceSpecification" => [
                "@type" => "PriceSpecification",
                "price" => "Contact for quote",
                "priceCurrency" => "INR"
            ]
        ]
    ];
}

/**
 * Get LocalBusiness Schema (for Contact page)
 */
function getLocalBusinessSchema() {
    return [
        "@context" => "https://schema.org",
        "@type" => "LocalBusiness",
        "@id" => "https://techweblabs.com/#organization",
        "name" => "TechWebLabs",
        "image" => "https://techweblabs.com/images/logo.png",
        "url" => "https://techweblabs.com",
        "telephone" => "+91-7670837961",
        "email" => "info@techweblabs.com",
        "address" => [
            "@type" => "PostalAddress",
            "streetAddress" => "Flat no 102, Plot no 1208, Spline Arcade, Ayyappa Society",
            "addressLocality" => "Madhapur",
            "addressRegion" => "Hyderabad",
            "postalCode" => "500081",
            "addressCountry" => "IN"
        ],
        "geo" => [
            "@type" => "GeoCoordinates",
            "latitude" => 17.4486,
            "longitude" => 78.3908
        ],
        "openingHoursSpecification" => [
            [
                "@type" => "OpeningHoursSpecification",
                "dayOfWeek" => ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
                "opens" => "09:00",
                "closes" => "18:00"
            ]
        ],
        "priceRange" => "$$",
        "currenciesAccepted" => "INR, USD",
        "paymentAccepted" => "Cash, Credit Card, Bank Transfer"
    ];
}

/**
 * Get Breadcrumb Schema
 */
function getBreadcrumbSchema($items) {
    $breadcrumbItems = [];
    $position = 1;
    
    foreach ($items as $name => $url) {
        $breadcrumbItems[] = [
            "@type" => "ListItem",
            "position" => $position,
            "name" => $name,
            "item" => $url
        ];
        $position++;
    }
    
    return [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => $breadcrumbItems
    ];
}

/**
 * Get FAQPage Schema
 */
function getFAQPageSchema($faqs) {
    $mainEntity = [];
    
    foreach ($faqs as $faq) {
        $mainEntity[] = [
            "@type" => "Question",
            "name" => $faq['question'],
            "acceptedAnswer" => [
                "@type" => "Answer",
                "text" => $faq['answer']
            ]
        ];
    }
    
    return [
        "@context" => "https://schema.org",
        "@type" => "FAQPage",
        "mainEntity" => $mainEntity
    ];
}

/**
 * Get SoftwareApplication Schema (for app development services)
 */
function getSoftwareApplicationSchema($appName, $appDescription) {
    return [
        "@context" => "https://schema.org",
        "@type" => "SoftwareApplication",
        "name" => $appName,
        "applicationCategory" => "BusinessApplication",
        "description" => $appDescription,
        "offers" => [
            "@type" => "Offer",
            "price" => "0",
            "priceCurrency" => "INR"
        ],
        "aggregateRating" => [
            "@type" => "AggregateRating",
            "ratingValue" => "4.8",
            "ratingCount" => "150"
        ],
        "operatingSystem" => ["iOS", "Android", "Web"]
    ];
}

/**
 * Output schema as JSON-LD script tag
 */
function outputSchema($schema) {
    return '<script type="application/ld+json">' . "\n" . 
           json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n" . 
           '</script>';
}

/**
 * Get all schemas for homepage
 */
function getHomepageSchemas() {
    return [
        'organization' => getOrganizationSchema(),
        'website' => getWebSiteSchema(),
        'breadcrumb' => getBreadcrumbSchema(['Home' => 'https://techweblabs.com']),
        'faq' => getFAQPageSchema([
            [
                'question' => 'Who is TechWebLabs?',
                'answer' => 'TechWebLabs is a leading mobile app and web development company based in Hyderabad, India. We specialize in custom app development, web solutions, AI software development, and digital transformation services for startups and enterprises worldwide.'
            ],
            [
                'question' => 'What services does TechWebLabs provide?',
                'answer' => 'TechWebLabs provides mobile app development (iOS, Android, Flutter, React Native), web development, AI software development, custom software solutions, MVP development, UI/UX design, and digital transformation services.'
            ],
            [
                'question' => 'Where is TechWebLabs located?',
                'answer' => 'TechWebLabs is headquartered in Hyderabad, India, and serves clients worldwide.'
            ],
            [
                'question' => 'How much does app development cost?',
                'answer' => 'App development costs vary based on features, complexity, and platform. Contact us for a free quote tailored to your project requirements.'
            ],
            [
                'question' => 'Do you offer Flutter app development?',
                'answer' => 'Yes, TechWebLabs is a leading Flutter app development company. We build cross-platform mobile apps using Flutter for iOS and Android.'
            ],
            [
                'question' => 'Do you work with startups?',
                'answer' => 'Yes, TechWebLabs specializes in startup app development. We help startups build MVPs, scale their applications, and transform their ideas into successful digital products.'
            ]
        ])
    ];
}

