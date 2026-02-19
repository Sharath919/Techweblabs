<?php
/**
 * SEO Optimizer - Generates SEO-optimized meta tags and content
 * for service pages targeting keywords like "swiggy clone application development"
 */

/**
 * Get SEO-optimized title for service pages
 */
function getSEOTitle($serviceName, $serviceType = 'clone') {
    $keywords = [
        'swiggy-clone' => 'Swiggy Clone App Development | Best Food Delivery Application Development Company',
        'zomato-clone' => 'Zomato Clone App Development | Restaurant Discovery & Food Ordering App',
        'pharmeasy-clone' => 'PharmEasy Clone App Development | Online Medicine Delivery Application',
        'uber-clone' => 'Uber Clone App Development | Taxi Booking Application Development Company',
        'ola-clone' => 'Ola Clone App Development | Ride-Sharing App Development Services',
        '1mg-clone' => '1mg Clone App Development | Online Pharmacy App Development Company',
        'bigbasket-clone' => 'BigBasket Clone App Development | Grocery Delivery Application Development',
        'blinkit-app' => 'Blinkit Clone App Development | Quick Commerce App Development Company',
        'instacart-clone' => 'Instacart Clone App Development | Grocery Shopping App Development',
        'food-delivery-app' => 'Food Delivery App Development | Best Restaurant Ordering App Development Company',
        'grocery-delivery-app' => 'Grocery Delivery App Development | Online Grocery Shopping App Development',
    ];
    
    $slug = strtolower(str_replace(' ', '-', $serviceName));
    if (isset($keywords[$slug])) {
        return $keywords[$slug] . ' | TechWebLabs';
    }
    
    // Generate dynamic title
    $serviceDisplay = ucwords(str_replace(['-', '_'], ' ', $serviceName));
    return $serviceDisplay . ' App Development | Best ' . $serviceDisplay . ' Application Development Company | TechWebLabs';
}

/**
 * Get SEO-optimized meta description
 */
function getSEODescription($serviceName, $serviceType = 'clone') {
    $descriptions = [
        'swiggy-clone' => 'Build a Swiggy clone app with TechWebLabs. Expert food delivery application development services. Custom Swiggy clone solutions for restaurants and food businesses. Get your food delivery app developed today.',
        'zomato-clone' => 'Develop a Zomato clone app with TechWebLabs. Professional restaurant discovery and food ordering application development. Custom Zomato clone solutions for food businesses.',
        'pharmeasy-clone' => 'Create a PharmEasy clone app with TechWebLabs. Expert online pharmacy and medicine delivery application development. Custom PharmEasy clone solutions for healthcare businesses.',
        'uber-clone' => 'Build an Uber clone app with TechWebLabs. Professional taxi booking and ride-sharing application development. Custom Uber clone solutions for transportation businesses.',
        'food-delivery-app' => 'Food delivery app development services by TechWebLabs. Build custom food ordering applications like Swiggy, Zomato. Expert food delivery app development company.',
        'grocery-delivery-app' => 'Grocery delivery app development by TechWebLabs. Build custom grocery shopping applications like BigBasket, Blinkit. Expert grocery delivery app development company.',
    ];
    
    $slug = strtolower(str_replace(' ', '-', $serviceName));
    if (isset($descriptions[$slug])) {
        return $descriptions[$slug];
    }
    
    // Generate dynamic description
    $serviceDisplay = ucwords(str_replace(['-', '_'], ' ', $serviceName));
    return 'Professional ' . $serviceDisplay . ' app development services by TechWebLabs. Custom ' . strtolower($serviceDisplay) . ' application development for startups and enterprises. Expert mobile app development company.';
}

/**
 * Get SEO keywords for service pages
 */
function getSEOKeywords($serviceName) {
    $baseKeywords = [
        'swiggy-clone' => 'swiggy clone app development, swiggy clone application development, food delivery app development, swiggy clone app, food ordering app development, restaurant app development, online food delivery app',
        'zomato-clone' => 'zomato clone app development, zomato clone application development, restaurant discovery app, food ordering app development, restaurant app development, zomato clone app',
        'pharmeasy-clone' => 'pharmeasy clone app development, pharmeasy clone application development, online pharmacy app development, medicine delivery app, healthcare app development, pharmeasy clone app',
        'uber-clone' => 'uber clone app development, uber clone application development, taxi booking app development, ride sharing app development, transportation app development, uber clone app',
        'food-delivery-app' => 'food delivery app development, food ordering app development, restaurant app development, food delivery application development, online food ordering app, food delivery app company',
        'grocery-delivery-app' => 'grocery delivery app development, grocery shopping app development, online grocery app, grocery delivery application development, grocery app development company',
    ];
    
    $slug = strtolower(str_replace(' ', '-', $serviceName));
    if (isset($baseKeywords[$slug])) {
        return $baseKeywords[$slug] . ', TechWebLabs, mobile app development company, app development services';
    }
    
    $serviceDisplay = ucwords(str_replace(['-', '_'], ' ', $serviceName));
    return strtolower($serviceDisplay) . ' app development, ' . strtolower($serviceDisplay) . ' application development, ' . strtolower($serviceDisplay) . ' app, mobile app development, TechWebLabs';
}

/**
 * Get SEO-optimized H1 tag
 */
function getSEOH1($serviceName) {
    $h1Tags = [
        'swiggy-clone' => 'Swiggy Clone App Development | Build Your Food Delivery Application',
        'zomato-clone' => 'Zomato Clone App Development | Restaurant Discovery & Food Ordering App',
        'pharmeasy-clone' => 'PharmEasy Clone App Development | Online Medicine Delivery Application',
        'uber-clone' => 'Uber Clone App Development | Taxi Booking Application Development',
        'food-delivery-app' => 'Food Delivery App Development | Build Your Restaurant Ordering Application',
        'grocery-delivery-app' => 'Grocery Delivery App Development | Online Grocery Shopping Application',
    ];
    
    $slug = strtolower(str_replace(' ', '-', $serviceName));
    if (isset($h1Tags[$slug])) {
        return $h1Tags[$slug];
    }
    
    $serviceDisplay = ucwords(str_replace(['-', '_'], ' ', $serviceName));
    return $serviceDisplay . ' App Development | Build Your ' . $serviceDisplay . ' Application';
}

/**
 * Get Service Schema for structured data
 */
function getServiceSchema($serviceName, $serviceUrl) {
    $serviceDisplay = ucwords(str_replace(['-', '_'], ' ', $serviceName));
    
    return [
        "@context" => "https://schema.org",
        "@type" => "Service",
        "serviceType" => $serviceDisplay . " App Development",
        "provider" => [
            "@type" => "Organization",
            "name" => "TechWebLabs",
            "url" => "https://techweblabs.com",
            "logo" => "https://techweblabs.com/images/logo.png"
        ],
        "areaServed" => [
            "@type" => "Country",
            "name" => "Worldwide"
        ],
        "description" => getSEODescription($serviceName),
        "url" => $serviceUrl
    ];
}

