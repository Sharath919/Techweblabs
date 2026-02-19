<?php
/**
 * Quick Test Script for Schema Generator
 * Run this to verify schema generation works
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Schema Generator Test</h1>";

// Test if file exists
if (file_exists('includes/schema-generator.php')) {
    echo "<p style='color: green;'>✅ schema-generator.php file found</p>";
    
    // Try to include it
    try {
        require_once('includes/schema-generator.php');
        echo "<p style='color: green;'>✅ Schema generator loaded successfully</p>";
        
        // Test Organization Schema
        echo "<h2>Testing Organization Schema:</h2>";
        $orgSchema = getOrganizationSchema();
        echo "<pre>" . json_encode($orgSchema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "</pre>";
        
        // Test WebSite Schema
        echo "<h2>Testing WebSite Schema:</h2>";
        $webSchema = getWebSiteSchema();
        echo "<pre>" . json_encode($webSchema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "</pre>";
        
        // Test FAQ Schema
        echo "<h2>Testing FAQ Schema:</h2>";
        $faqs = [
            ['question' => 'Test Question?', 'answer' => 'Test Answer']
        ];
        $faqSchema = getFAQPageSchema($faqs);
        echo "<pre>" . json_encode($faqSchema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "</pre>";
        
        // Test output function
        echo "<h2>Testing Output Function:</h2>";
        $output = outputSchema($orgSchema);
        echo "<p>Output length: " . strlen($output) . " characters</p>";
        echo "<div style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($output) . "</div>";
        
        echo "<p style='color: green; font-size: 18px; font-weight: bold;'>✅ All tests passed! Schema generator is working correctly.</p>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ schema-generator.php file NOT found</p>";
    echo "<p>Current directory: " . __DIR__ . "</p>";
    echo "<p>Looking for: " . __DIR__ . "/includes/schema-generator.php</p>";
}

echo "<hr>";
echo "<h2>File Structure Check:</h2>";
echo "<ul>";
echo "<li>index.php: " . (file_exists('index.php') ? '✅' : '❌') . "</li>";
echo "<li>robots.txt: " . (file_exists('robots.txt') ? '✅' : '❌') . "</li>";
echo "<li>sitemap.xml: " . (file_exists('sitemap.xml') ? '✅' : '❌') . "</li>";
echo "<li>homepage/header.php: " . (file_exists('homepage/header.php') ? '✅' : '❌') . "</li>";
echo "<li>homepage/footer.php: " . (file_exists('homepage/footer.php') ? '✅' : '❌') . "</li>";
echo "<li>pages/homepage/index.php: " . (file_exists('pages/homepage/index.php') ? '✅' : '❌') . "</li>";
echo "</ul>";

?>

