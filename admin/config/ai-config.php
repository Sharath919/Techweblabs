<?php
/**
 * AI Configuration (legacy PHP admin — prefer Next.js + Anthropic on Vercel)
 *
 * Set ANTHROPIC_API_KEY in environment variables.
 */

define('ANTHROPIC_API_KEY', getenv('ANTHROPIC_API_KEY') ?: '');
define('ANTHROPIC_MODEL', getenv('ANTHROPIC_MODEL') ?: 'claude-sonnet-4-20250514');

/** @deprecated use ANTHROPIC_API_KEY */
define('OPENAI_API_KEY', getenv('OPENAI_API_KEY') ?: '');
define('OPENAI_MODEL', getenv('OPENAI_MODEL') ?: 'gpt-4o-mini');

if (!defined('SITE_BASE_URL')) {
    define('SITE_BASE_URL', 'https://techweblabs.com');
}

function isAIConfigured() {
    return !empty(getAIAPIKey());
}

function getAIAPIKey() {
    if (getenv('ANTHROPIC_API_KEY')) {
        return getenv('ANTHROPIC_API_KEY');
    }
    return getenv('OPENAI_API_KEY') ?: '';
}

function getAIModel() {
    if (getenv('ANTHROPIC_MODEL')) {
        return getenv('ANTHROPIC_MODEL');
    }
    return getenv('OPENAI_MODEL') ?: 'claude-sonnet-4-20250514';
}
