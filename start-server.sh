#!/bin/bash
# Start PHP development server with router
# Usage: ./start-server.sh

cd "$(dirname "$0")"
echo "🚀 Starting PHP server on http://localhost:8000"
echo "📁 Using router.php for URL routing"
echo ""
echo "Press Ctrl+C to stop the server"
echo ""
php -S localhost:8000 router.php

