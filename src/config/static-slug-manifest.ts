/**
 * Static page slug manifest — mirrors .htaccess SEO URL mappings.
 */

export const STATIC_SLUG_MAP: Record<string, string> = {
  '/': '/',
  about: '/about',
  careers: '/careers',
  contact: '/contact',
  'full-stack-development-course-with-guaranteed-placement':
    '/full-stack-development-course-with-guaranteed-placement',
  'food-delivery-app-development': '/food-delivery-app-development',
  'grocery-app-development': '/grocery-app-development',
  'on-demand-home-services-app-development': '/on-demand-home-services-app-development',
  'on-demand-taxi-booking-app-development': '/on-demand-taxi-booking-app-development',
  'healthcare-mobile-app-development': '/healthcare-mobile-app-development',
  'handyman-mobile-app-development': '/handyman-mobile-app-development',
  'fitness-mobile-app-development-company': '/fitness-mobile-app-development-company',
  'pet-care-app-development': '/pet-care-app-development',
  'beauty-salon-app-development-company': '/beauty-salon-app-development-company',
  'education-app-development': '/education-app-development',
  'real-estate-app-development': '/real-estate-app-development',
  'ecommerce-app-development': '/ecommerce-app-development',
  'car-rental-app-development': '/car-rental-app-development',
  'beauty-services-app-development': '/beauty-services-app-development',
  'doctor-consultation-app-development': '/doctor-consultation-app-development',
  'social-media-app-development': '/social-media-app-development',
  'logistics-transportation-app-development': '/logistics-transportation-app-development',
  'e-learning-app-development': '/e-learning-app-development',
  'job-portal-app-development': '/job-portal-app-development',
  'cashback-app-development': '/cashback-app-development',
  'ott-platform-app-development': '/ott-platform-app-development',
  'dating-app-development': '/dating-app-development',
  'chat-app-development-company': '/chat-app-development-company',
  'erp-software-development': '/erp-software-development',
  'crm-software-development': '/crm-software-development',
  'hrm-software-development': '/hrm-software-development',
  'inventory-management-software-development': '/inventory-management-software-development',
  'general-marketplaces-app': '/general-marketplaces-app',
}

export const DIRECT_PHP_SLUGS = [
  'swiggy-clone', 'zomato-clone', 'ubereats-app', 'doordash-app', 'grubhub-app',
  'postmates-clone', 'postmates-clone-app', 'deliveroo-app', 'instacart-clone',
  'amazon-fresh-app', 'zepto-app', 'bigbasket-clone', 'blinkit-app', 'blinkit-clone',
  'swiggy-instamart-clone', 'uber-clone', 'lyft-clone', 'ola-clone', 'grab-clone',
  'pharmeasy-clone', '1mg-clone', 'urban-clone-app', 'laundr-clone-app', 'Porter-clone-app',
  'android-app-development', 'ios-app-development', 'custom-app-development',
  'cross-platform-app', 'mvp-development-app', 'full-stack-development', 'about-us',
  'terms-and-conditions', 'terms-and-condition', 'refund-policy', 'admin-panels',
  'affiliate-marketing', 'analytics-reporting', 'app-store-optimization', 'content-marketing',
  'email-marketing', 'search-engine-marketing', 'social-media-marketing', 'online-reputation',
  'graphic-design-service', 'mobile-app-designing', 'mobile-app-maintenance',
  'website-maintenance', 'website-testing', 'custom-website-development', 'e-commerce-website',
  'responsive-Web-design', 'wireframing-services', 'Prototype-Design-Services',
  'visual-design-services', 'intaraction-desgin-services', 'accessibility-compliance-services',
  'User-Research', 'Information-Architecture', 'landing-Pages',
]

export function getAllStaticSlugs(): string[] {
  const fromMap = Object.keys(STATIC_SLUG_MAP).filter((k) => k !== '/')
  return [...new Set([...fromMap, ...DIRECT_PHP_SLUGS])]
}

export function getStaticSitemapPaths(): Array<{ loc: string; priority: string; changefreq: string }> {
  return [
    { loc: '/', priority: '1.0', changefreq: 'daily' },
    ...getAllStaticSlugs().map((slug) => ({
      loc: `/${slug}`,
      priority: '0.8',
      changefreq: 'monthly',
    })),
  ]
}
