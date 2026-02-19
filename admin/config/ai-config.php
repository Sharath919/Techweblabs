<?php
/**
 * AI Configuration
 *
 * Set OPENAI_API_KEY in environment variables — never commit keys to git.
 * Get your API key from: https://platform.openai.com/api-keys
 */

define('OPENAI_API_KEY', getenv('OPENAI_API_KEY') ?: '');
define('OPENAI_MODEL', getenv('OPENAI_MODEL') ?: 'gpt-4o-mini');

if (!defined('SITE_BASE_URL')) {
    define('SITE_BASE_URL', 'https://techweblabs.com');
}

function isAIConfigured() {
    return !empty(getAIAPIKey());
}

function getAIAPIKey() {
    return getenv('OPENAI_API_KEY') ?: (defined('OPENAI_API_KEY') ? OPENAI_API_KEY : '');
}

function getAIModel() {
    return defined('OPENAI_MODEL') ? OPENAI_MODEL : 'gpt-4o-mini';
}
