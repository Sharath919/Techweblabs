<?php
/**
 * SEO Fix Script - Apply SEO optimizations to service pages
 * 
 * This script helps identify and fix SEO issues across all service pages
 * 
 * Usage: Review the output to see which pages need SEO fixes
 */

require_once('config.php');
require_once('includes/seo-optimizer.php');

$servicePages = [
    'swiggy-clone' => [
        'title' => 'Swiggy Clone App Development | Best Food Delivery Application Development Company',
        'description' => 'Build a Swiggy clone app with TechWebLabs. Expert food delivery application development services. Custom Swiggy clone solutions for restaurants and food businesses.',
        'keywords' => 'swiggy clone app development, swiggy clone application development, food delivery app development',
        'h1' => 'Swiggy Clone App Development | Build Your Food Delivery Application'
    ],
    'zomato-clone' => [
        'title' => 'Zomato Clone App Development | Restaurant Discovery & Food Ordering App Development',
        'description' => 'Develop a Zomato clone app with TechWebLabs. Professional restaurant discovery and food ordering application development. Custom Zomato clone solutions.',
        'keywords' => 'zomato clone app development, zomato clone application development, restaurant app development',
        'h1' => 'Zomato Clone App Development | Restaurant Discovery & Food Ordering App'
    ],
    'pharmeasy-clone' => [
        'title' => 'PharmEasy Clone App Development | Online Medicine Delivery Application Development',
        'description' => 'Create a PharmEasy clone app with TechWebLabs. Expert online pharmacy and medicine delivery application development. Custom PharmEasy clone solutions.',
        'keywords' => 'pharmeasy clone app development, pharmeasy clone application development, online pharmacy app development',
        'h1' => 'PharmEasy Clone App Development | Online Medicine Delivery Application'
    ],
    'uber-clone' => [
        'title' => 'Uber Clone App Development | Taxi Booking Application Development Company',
        'description' => 'Build an Uber clone app with TechWebLabs. Professional taxi booking and ride-sharing application development. Custom Uber clone solutions.',
        'keywords' => 'uber clone app development, uber clone application development, taxi booking app development',
        'h1' => 'Uber Clone App Development | Taxi Booking Application Development'
    ],
    'food-delivery-app' => [
        'title' => 'Food Delivery App Development | Best Restaurant Ordering App Development Company',
        'description' => 'Food delivery app development services by TechWebLabs. Build custom food ordering applications like Swiggy, Zomato. Expert food delivery app development.',
        'keywords' => 'food delivery app development, food ordering app development, restaurant app development',
        'h1' => 'Food Delivery App Development | Build Your Restaurant Ordering Application'
    ],
    'grocery-delivery-app' => [
        'title' => 'Grocery Delivery App Development | Online Grocery Shopping App Development',
        'description' => 'Grocery delivery app development by TechWebLabs. Build custom grocery shopping applications like BigBasket, Blinkit. Expert grocery delivery app development.',
        'keywords' => 'grocery delivery app development, grocery shopping app development, online grocery app',
        'h1' => 'Grocery Delivery App Development | Online Grocery Shopping Application'
    ],
];

echo "SEO Optimization Guide for Service Pages\n";
echo "========================================\n\n";

foreach ($servicePages as $slug => $seo) {
    $file = ROOT_DIR . 'pages/ondemand/' . $slug . '.php';
    if (file_exists($file)) {
        echo "Page: $slug\n";
        echo "Title: " . $seo['title'] . "\n";
        echo "Description: " . $seo['description'] . "\n";
        echo "H1: " . $seo['h1'] . "\n";
        echo "Keywords: " . $seo['keywords'] . "\n";
        echo "---\n\n";
    }
}

echo "\nTo apply these optimizations:\n";
echo "1. Remove duplicate <head> sections\n";
echo "2. Update title tags\n";
echo "3. Update meta descriptions\n";
echo "4. Update H1 tags\n";
echo "5. Add proper Service schema\n";
echo "6. Add target keywords to content\n";

