# Server 500 Error - Fix Guide
## Common Causes & Solutions

---

## 🔴 MOST COMMON CAUSES

### 1. **Path Issues with ROOT_DIR** (Most Likely)
**Problem:** `config.php` uses `$_SERVER['DOCUMENT_ROOT']` which might point to wrong directory on server.

**Fix:** Update `config.php` to handle both local and server environments:

```php
<?php
// Auto-detect root directory
if (!defined('ROOT_DIR')) {
    // Try to detect if we're in public_html or root
    $possibleRoots = [
        __DIR__,  // Current directory
        dirname(__DIR__),  // Parent directory
        $_SERVER['DOCUMENT_ROOT']  // Server document root
    ];
    
    // Use the directory that contains 'homepage' folder
    foreach ($possibleRoots as $root) {
        if (is_dir($root . '/homepage')) {
            define('ROOT_DIR', $root . '/');
            break;
        }
    }
    
    // Fallback to document root
    if (!defined('ROOT_DIR')) {
        define('ROOT_DIR', $_SERVER['DOCUMENT_ROOT'] . '/');
    }
}
?>
```

---

### 2. **Missing config.php Include**
**Problem:** Some files use `ROOT_DIR` but don't include `config.php`.

**Fix:** Add to top of files that use `ROOT_DIR`:
```php
<?php
require_once(__DIR__ . '/../../config.php');  // Adjust path as needed
?>
```

---

### 3. **.htaccess Issues**
**Problem:** Some `.htaccess` rules might not work on your server.

**Fix:** Temporarily rename `.htaccess` to `.htaccess.backup` and test:
```bash
mv .htaccess .htaccess.backup
```

If site works, then `.htaccess` is the issue. Re-enable rules one by one.

---

### 4. **PHP Version Compatibility**
**Problem:** Server might have older PHP version.

**Fix:** Check PHP version:
```php
<?php phpinfo(); ?>
```

Minimum required: PHP 7.4+

---

### 5. **File Permissions**
**Problem:** Files might not have correct permissions.

**Fix:** Set correct permissions:
```bash
# Set directory permissions
find . -type d -exec chmod 755 {} \;

# Set file permissions
find . -type f -exec chmod 644 {} \;

# Make PHP files executable
find . -name "*.php" -exec chmod 644 {} \;
```

---

### 6. **Missing PHP Extensions**
**Problem:** Server might be missing required PHP extensions.

**Check:** Create `phpinfo.php`:
```php
<?php phpinfo(); ?>
```

**Required Extensions:**
- mod_rewrite (for .htaccess)
- mbstring
- json
- curl

---

## 🔧 STEP-BY-STEP FIX

### Step 1: Enable Error Display
Create `error-check.php` in public_html:
```php
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "PHP Version: " . phpversion() . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script Name: " . __FILE__ . "<br>";
echo "Current Dir: " . __DIR__ . "<br>";

// Test config
require_once('config.php');
echo "ROOT_DIR: " . ROOT_DIR . "<br>";

// Test if homepage folder exists
if (is_dir(ROOT_DIR . 'homepage')) {
    echo "✅ homepage folder found<br>";
} else {
    echo "❌ homepage folder NOT found<br>";
}
?>
```

Access: `https://yourdomain.com/error-check.php`

---

### Step 2: Fix config.php
Update `config.php` with smart path detection:

```php
<?php
// Smart path detection for server compatibility
if (!defined('ROOT_DIR')) {
    // Get the directory where config.php is located
    $configDir = __DIR__;
    
    // Check if homepage folder exists in current directory
    if (is_dir($configDir . '/homepage')) {
        define('ROOT_DIR', $configDir . '/');
    }
    // Check if homepage folder exists in parent directory
    elseif (is_dir(dirname($configDir) . '/homepage')) {
        define('ROOT_DIR', dirname($configDir) . '/');
    }
    // Fallback to document root
    else {
        $docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__FILE__);
        // Remove trailing slash if present
        $docRoot = rtrim($docRoot, '/');
        define('ROOT_DIR', $docRoot . '/');
    }
}
?>
```

---

### Step 3: Check .htaccess
**Option A:** Temporarily disable .htaccess:
```bash
mv .htaccess .htaccess.disabled
```

**Option B:** Simplify .htaccess (remove problematic rules):
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    # Only essential rewrites
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^ index.php [L]
</IfModule>

DirectoryIndex index.php
```

---

### Step 4: Check File Structure
Ensure all files are uploaded:
```
public_html/
├── index.php
├── config.php
├── .htaccess
├── homepage/
│   ├── header.php
│   └── footer.php
├── pages/
│   ├── homepage/
│   └── ondemand/
├── images/
├── css/
├── js/
└── includes/
```

---

### Step 5: Test Individual Files
Test each component:

1. **Test config.php:**
```php
<?php
require_once('config.php');
echo ROOT_DIR;
?>
```

2. **Test header.php:**
```php
<?php
require_once('config.php');
include(ROOT_DIR . 'homepage/header.php');
?>
```

3. **Test index.php:**
```php
<?php
require_once('config.php');
// Rest of index.php
?>
```

---

## 🚨 QUICK FIX (Try This First)

### Update config.php:
```php
<?php
if (!defined('ROOT_DIR')) {
    // Use __DIR__ which is more reliable
    $root = __DIR__;
    
    // If we're in a subdirectory, go up one level
    if (basename($root) === 'public_html' || basename($root) === 'htdocs') {
        // We're in the root, use it
        define('ROOT_DIR', $root . '/');
    } else {
        // Try to find the root
        $parent = dirname($root);
        if (is_dir($parent . '/homepage')) {
            define('ROOT_DIR', $parent . '/');
        } else {
            define('ROOT_DIR', $root . '/');
        }
    }
}
?>
```

---

## 📋 CHECKLIST

- [ ] Check PHP version (should be 7.4+)
- [ ] Check file permissions (755 for dirs, 644 for files)
- [ ] Verify all files uploaded
- [ ] Test config.php path detection
- [ ] Check .htaccess rules
- [ ] Enable error display to see actual error
- [ ] Check server error logs

---

## 🔍 CHECK SERVER ERROR LOGS

**cPanel:** 
- Error Logs section
- Look for PHP errors

**SSH:**
```bash
tail -f /home/username/logs/error_log
# or
tail -f /var/log/apache2/error.log
```

---

## 💡 ALTERNATIVE: Use Relative Paths

If `ROOT_DIR` continues to cause issues, use relative paths:

**Instead of:**
```php
include(ROOT_DIR . 'homepage/header.php');
```

**Use:**
```php
include(__DIR__ . '/../homepage/header.php');
```

---

## 🆘 IF STILL NOT WORKING

1. **Enable error display:**
```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

2. **Check server requirements:**
   - PHP 7.4+
   - mod_rewrite enabled
   - mbstring extension
   - json extension

3. **Contact hosting support** with:
   - PHP version
   - Error log contents
   - .htaccess rules

---

## ✅ VERIFICATION

After fixes, test:
1. Homepage loads: `https://yourdomain.com/`
2. Service page loads: `https://yourdomain.com/swiggy-clone`
3. No 500 errors
4. Images load correctly
5. CSS/JS load correctly

