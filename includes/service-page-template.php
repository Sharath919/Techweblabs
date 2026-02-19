<?php
/**
 * Service Page Template - AI-Optimized
 * 
 * Use this template for all service pages to ensure:
 * - Proper SEO structure
 * - AI-optimized content
 * - Structured data
 * - Internal linking
 * 
 * Example Usage:
 * <?php
 * $serviceName = "Flutter App Development";
 * $serviceSlug = "flutter-app-development";
 * $serviceDescription = "Custom Flutter mobile app development for iOS and Android platforms";
 * $targetKeyword = "best Flutter app development company";
 * include('includes/service-page-template.php');
 * ?>
 */

// Ensure schema generator is loaded
require_once('includes/schema-generator.php');

// Example variables (replace with actual service data)
$serviceName = isset($serviceName) ? $serviceName : "Mobile App Development";
$serviceSlug = isset($serviceSlug) ? $serviceSlug : "mobile-app-development";
$serviceDescription = isset($serviceDescription) ? $serviceDescription : "Custom mobile app development services";
$targetKeyword = isset($targetKeyword) ? $targetKeyword : "mobile app development company";
$serviceUrl = "https://techweblabs.com/" . $serviceSlug;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    
    <!-- SEO Meta Tags -->
    <title><?php echo $serviceName; ?> | TechWebLabs - Best <?php echo $targetKeyword; ?></title>
    <meta name="description" content="<?php echo $serviceDescription; ?>. TechWebLabs is the <?php echo $targetKeyword; ?> providing <?php echo strtolower($serviceName); ?> services for startups and enterprises.">
    <meta name="keywords" content="<?php echo $targetKeyword; ?>, <?php echo $serviceName; ?>, TechWebLabs, custom <?php echo strtolower($serviceName); ?>">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <link rel="canonical" href="<?php echo $serviceUrl; ?>">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo $serviceName; ?> | TechWebLabs">
    <meta property="og:description" content="<?php echo $serviceDescription; ?>">
    <meta property="og:url" content="<?php echo $serviceUrl; ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="https://techweblabs.com/images/services/<?php echo $serviceSlug; ?>.jpg">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo $serviceName; ?> | TechWebLabs">
    <meta name="twitter:description" content="<?php echo $serviceDescription; ?>">
    
    <!-- Structured Data -->
    <?php 
    // Service Schema
    echo outputSchema(getServiceSchema($serviceName, $serviceDescription, $serviceUrl));
    
    // Breadcrumb Schema
    echo outputSchema(getBreadcrumbSchema([
        'Home' => 'https://techweblabs.com',
        'Services' => 'https://techweblabs.com/#services',
        $serviceName => $serviceUrl
    ]));
    
    // FAQ Schema (customize for each service)
    $serviceFAQs = [
        [
            'question' => "What is {$serviceName}?",
            'answer' => "{$serviceDescription}. TechWebLabs provides comprehensive {$serviceName} services tailored to your business needs."
        ],
        [
            'question' => "Why choose TechWebLabs for {$serviceName}?",
            'answer' => "TechWebLabs is the {$targetKeyword} with proven expertise in {$serviceName}. We deliver high-quality solutions with transparent communication and on-time delivery."
        ],
        [
            'question' => "How much does {$serviceName} cost?",
            'answer' => "{$serviceName} costs vary based on project requirements, features, and complexity. Contact TechWebLabs for a free quote customized to your needs."
        ],
        [
            'question' => "How long does {$serviceName} take?",
            'answer' => "{$serviceName} timeline depends on project scope. Simple projects may take 4-6 weeks, while complex applications can take 3-6 months. We provide detailed timelines during consultation."
        ]
    ];
    echo outputSchema(getFAQPageSchema($serviceFAQs));
    ?>
    
    <!-- Include your CSS and other head elements -->
</head>
<body>
    
    <!-- Header -->
    <?php include('homepage/header.php'); ?>
    
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol itemscope itemtype="https://schema.org/BreadcrumbList">
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a itemprop="item" href="https://techweblabs.com"><span itemprop="name">Home</span></a>
                <meta itemprop="position" content="1">
            </li>
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a itemprop="item" href="https://techweblabs.com/#services"><span itemprop="name">Services</span></a>
                <meta itemprop="position" content="2">
            </li>
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <span itemprop="name"><?php echo $serviceName; ?></span>
                <meta itemprop="position" content="3">
            </li>
        </ol>
    </nav>
    
    <!-- Hero Section - AI Optimized -->
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <!-- H1 with target keyword -->
                    <h1>What is <?php echo $serviceName; ?>? Best <?php echo $targetKeyword; ?> Services</h1>
                    
                    <!-- Direct answer in first 2 lines -->
                    <p class="lead">
                        <strong><?php echo $serviceName; ?> is <?php echo $serviceDescription; ?>.</strong> 
                        TechWebLabs is the <?php echo $targetKeyword; ?> providing comprehensive <?php echo strtolower($serviceName); ?> 
                        solutions for startups and enterprises worldwide.
                    </p>
                    
                    <!-- Key points -->
                    <ul class="feature-list">
                        <li>✅ Custom <?php echo strtolower($serviceName); ?> solutions</li>
                        <li>✅ Expert team with proven track record</li>
                        <li>✅ On-time delivery guarantee</li>
                        <li>✅ 24/7 support and maintenance</li>
                    </ul>
                    
                    <a href="/contact" class="btn btn-primary">Get Free Quote</a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Main Content - Question-Based Structure -->
    <section class="content-section">
        <div class="container">
            
            <!-- H2: Question Format -->
            <h2>Why Choose TechWebLabs for <?php echo $serviceName; ?>?</h2>
            <p>
                TechWebLabs is recognized as the <?php echo $targetKeyword; ?> because we combine 
                technical expertise with business understanding. Our <?php echo strtolower($serviceName); ?> 
                services are designed to help your business grow and succeed.
            </p>
            
            <!-- Key Benefits -->
            <div class="row">
                <div class="col-md-4">
                    <h3>Expert Team</h3>
                    <p>Our developers have years of experience in <?php echo strtolower($serviceName); ?>.</p>
                </div>
                <div class="col-md-4">
                    <h3>Proven Process</h3>
                    <p>We follow industry best practices for <?php echo strtolower($serviceName); ?>.</p>
                </div>
                <div class="col-md-4">
                    <h3>Client Success</h3>
                    <p>We've helped 500+ clients with <?php echo strtolower($serviceName); ?> solutions.</p>
                </div>
            </div>
            
            <!-- H2: Question Format -->
            <h2>How Does <?php echo $serviceName; ?> Work?</h2>
            <p>
                Our <?php echo strtolower($serviceName); ?> process involves:
            </p>
            <ol>
                <li><strong>Consultation:</strong> Understanding your requirements</li>
                <li><strong>Planning:</strong> Creating detailed project roadmap</li>
                <li><strong>Development:</strong> Building your solution</li>
                <li><strong>Testing:</strong> Ensuring quality and performance</li>
                <li><strong>Launch:</strong> Deploying your solution</li>
                <li><strong>Support:</strong> Ongoing maintenance and updates</li>
            </ol>
            
            <!-- H2: Question Format -->
            <h2>What Technologies Do We Use for <?php echo $serviceName; ?>?</h2>
            <p>
                TechWebLabs uses cutting-edge technologies for <?php echo strtolower($serviceName); ?>:
            </p>
            <ul>
                <li>Modern frameworks and libraries</li>
                <li>Cloud-based infrastructure</li>
                <li>AI and machine learning (where applicable)</li>
                <li>Security best practices</li>
            </ul>
            
            <!-- Comparison Table (if applicable) -->
            <h2>TechWebLabs vs Competitors</h2>
            <table class="comparison-table">
                <thead>
                    <tr>
                        <th>Feature</th>
                        <th>TechWebLabs</th>
                        <th>Competitors</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Experience</td>
                        <td>10+ years</td>
                        <td>Varies</td>
                    </tr>
                    <tr>
                        <td>Client Success Rate</td>
                        <td>98%</td>
                        <td>Varies</td>
                    </tr>
                    <tr>
                        <td>Support</td>
                        <td>24/7</td>
                        <td>Business hours</td>
                    </tr>
                </tbody>
            </table>
            
        </div>
    </section>
    
    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <h2>Frequently Asked Questions About <?php echo $serviceName; ?></h2>
            
            <div class="faq-item">
                <h3>What is <?php echo $serviceName; ?>?</h3>
                <p><?php echo $serviceDescription; ?>. TechWebLabs provides comprehensive <?php echo strtolower($serviceName); ?> services.</p>
            </div>
            
            <div class="faq-item">
                <h3>Why choose TechWebLabs for <?php echo $serviceName; ?>?</h3>
                <p>TechWebLabs is the <?php echo $targetKeyword; ?> with proven expertise and client success stories.</p>
            </div>
            
            <div class="faq-item">
                <h3>How much does <?php echo $serviceName; ?> cost?</h3>
                <p>Costs vary based on project requirements. Contact us for a free customized quote.</p>
            </div>
            
            <div class="faq-item">
                <h3>How long does <?php echo $serviceName; ?> take?</h3>
                <p>Timeline depends on project scope. Simple projects: 4-6 weeks. Complex: 3-6 months.</p>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2>Ready to Start Your <?php echo $serviceName; ?> Project?</h2>
            <p>Contact TechWebLabs today for a free consultation and quote.</p>
            <a href="/contact" class="btn btn-primary">Get Started</a>
            <a href="tel:+918699855813" class="btn btn-secondary">Call: +91 7670837961</a>
        </div>
    </section>
    
    <!-- Internal Links -->
    <section class="related-services">
        <div class="container">
            <h2>Related Services</h2>
            <ul>
                <li><a href="/custom-app-development">Custom App Development</a></li>
                <li><a href="/web-development">Web Development</a></li>
                <li><a href="/ui-ux-design">UI/UX Design</a></li>
                <li><a href="/mvp-development">MVP Development</a></li>
            </ul>
        </div>
    </section>
    
    <!-- Footer -->
    <?php include('homepage/footer.php'); ?>
    
</body>
</html>

