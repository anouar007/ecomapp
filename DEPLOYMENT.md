# Deployment Checklist

## Before Deploying to Production

### 1. Environment Setup
- [ ] Copy `.env.production` to `.env` on server
- [ ] Update `APP_URL` with your domain
- [ ] Update database credentials (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`)
- [ ] Update mail settings (`MAIL_HOST`, `MAIL_USERNAME`, `MAIL_PASSWORD`)
- [ ] Generate new `APP_KEY` with `php artisan key:generate`

### 2. Database
- [ ] Create production database
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Seed initial data: `php artisan db:seed --force`

### 3. Performance Optimization
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

### 4. File Permissions
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 5. SSL & Security
- [ ] Install SSL certificate (Let's Encrypt recommended)
- [ ] Enable `SESSION_SECURE_COOKIE=true` in .env
- [ ] Verify `APP_DEBUG=false` is set

### 6. Final Verification
- [ ] Visit home page and verify it loads
- [ ] Test login/logout functionality
- [ ] Place a test order
- [ ] Check admin dashboard access

---
**Project Status: ✅ Ready for Delivery**
- All 58 tests passing
- Logs cleared
- Production config template ready
