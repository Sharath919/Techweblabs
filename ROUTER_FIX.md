# Router Fix Instructions

## The Problem
When using PHP's built-in server (`php -S localhost:8000`), all pages show the homepage because:
1. PHP built-in server doesn't process `.htaccess` files
2. You MUST use `router.php` to handle URL routing

## The Solution

### Step 1: Stop Your Current Server
Press `Ctrl+C` in the terminal where the server is running.

### Step 2: Start Server with Router
```bash
cd /Applications/XAMPP/xamppfiles/htdocs/techweblabs_live
php -S localhost:8000 router.php
```

**IMPORTANT:** You must include `router.php` at the end of the command!

### Step 3: Test the Pages
- Homepage: http://localhost:8000/
- PharmEasy Clone: http://localhost:8000/pharmeasy-clone
- About: http://localhost:8000/about
- Contact: http://localhost:8000/contact

## What I Fixed

1. ✅ Updated `router.php` to set `DOCUMENT_ROOT` correctly (required by config.php)
2. ✅ Router now properly maps `/pharmeasy-clone` → `/pages/ondemand/pharmeasy-clone.php`
3. ✅ Added trailing slash handling
4. ✅ Improved static file detection

## Verification

The router has been tested and confirmed working. The file `/pages/ondemand/pharmeasy-clone.php` exists and will be loaded correctly when you use `router.php`.

## If Still Not Working

1. **Check you're using router.php:**
   ```bash
   # WRONG (won't work):
   php -S localhost:8000
   
   # CORRECT (will work):
   php -S localhost:8000 router.php
   ```

2. **Clear browser cache:**
   - Hard refresh: `Ctrl+Shift+R` (Windows) or `Cmd+Shift+R` (Mac)

3. **Check for PHP errors:**
   ```bash
   php -S localhost:8000 router.php 2>&1 | grep -i error
   ```

4. **Test router directly:**
   ```bash
   php test-router.php
   ```

