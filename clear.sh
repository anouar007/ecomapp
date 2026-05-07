#!/bin/bash

echo "🚀 Clearing Laravel Cache..."

# Clear all caches
php artisan optimize:clear

# Optional: Re-cache if in production (commented out for dev)
# php artisan config:cache
# php artisan route:cache
# php artisan view:cache

echo "✅ All caches cleared successfully!"
