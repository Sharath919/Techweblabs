# Debugging 500 Error - Step by Step

## 🚨 If Even test-server.php Shows 500 Error

This means the issue is **BEFORE** PHP execution - likely:
1. PHP syntax error in a file that's auto-loaded
2. `.htaccess` causing fatal error
3. Server configuration issue
4. File encoding issue

---

## Step 1: Test PHP Execution

### Create `test-minimal.php`:
```php
<?php
echo "PHP is working!";
phpinfo();
?>
```

**Upload and access:** `https://yourdomain.com/test-minimal.php`

**If this ALSO shows 500:**
- PHP is not executing at all
- Contact hosting support
- Check if PHP is enabled for your domain

**If this WORKS:**
- PHP is working, issue is in your code
- Continue to Step 2

---

## Step 2: Disable .htaccess

**Rename `.htaccess`:**
```bash
mv .htaccess .htaccess.disabled
```

**Or delete it temporarily:**
```bash
rm .htaccess
```

**Test again:**
- Access `test-minimal.php`
- Access homepage

**If site works now:**
- `.htaccess` is the problem
- Use `.htaccess.minimal` instead
- Gradually add rules back

---

## Step 3: Check for Auto-Loaded Files

Some servers auto-load files. Check for:
- `auto_prepend_file` in php.ini
- `.user.ini` file
- `php.ini` in public_html

**Create `.user.ini` to disable auto-prepend:**
```ini
auto_prepend_file =
```

---

## Step 4: Check File Encoding

**Issue:** Files might have BOM (Byte Order Mark) or wrong encoding

**Fix:** Re-save files as UTF-8 without BOM

**Check:** Open files in text editor, check encoding

---

## Step 5: Check Server Error Logs

**cPanel:**
- Error Logs section
- Look for PHP fatal errors

**Common errors:**
- `Parse error: syntax error`
- `Fatal error: require()`
- `Call to undefined function`
- `Cannot redeclare`

---

## Step 6: Test Individual Files

### Test config.php:
```php
<?php
// Just the config.php content
if (!defined('ROOT_DIR')) {
    $configDir = __DIR__;
    if (is_dir($configDir . '/homepage')) {
        define('ROOT_DIR', $configDir . '/');
    } else {
        $docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : $configDir;
        $docRoot = rtrim($docRoot, '/\\');
        define('ROOT_DIR', $docRoot . '/');
    }
}
echo "ROOT_DIR: " . ROOT_DIR;
?>
```

### Test index.php header:
```php
<?php
require_once(__DIR__ . '/config.php');
echo "Config loaded. ROOT_DIR: " . ROOT_DIR;
?>
```

---

## Step 7: Check PHP Version Compatibility

**Check PHP version:**
```php
<?php
echo phpversion();
?>
```

**Required:** PHP 7.4 or higher

**If lower:** Contact hosting to upgrade

---

## Step 8: Check File Permissions

**Via cPanel File Manager:**
- Folders: 755
- Files: 644
- PHP files: 644

**Via SSH:**
```bash
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
```

---

## Step 9: Check for Hidden Characters

**Issue:** Invisible characters causing parse errors

**Fix:** 
1. Open file in text editor
2. Show all characters
3. Remove any hidden characters
4. Re-save as UTF-8

---

## Step 10: Minimal Working Setup

### Create `index-test.php`:
```php
<?php
echo "Hello World!";
?>
```

**If this works:**
- Server is fine
- Issue is in your code

**If this doesn't work:**
- Server configuration issue
- Contact hosting support

---

## 🔍 Most Common Causes

1. **.htaccess error** (80% of cases)
   - Solution: Disable .htaccess

2. **PHP syntax error** (15% of cases)
   - Solution: Check error logs

3. **Missing PHP extension** (3% of cases)
   - Solution: Enable in php.ini

4. **File encoding** (2% of cases)
   - Solution: Re-save as UTF-8

---

## ✅ Quick Fix Checklist

- [ ] Test `test-minimal.php` (just `<?php echo "test"; ?>`)
- [ ] Disable `.htaccess` (rename to `.htaccess.disabled`)
- [ ] Check server error logs
- [ ] Check PHP version (need 7.4+)
- [ ] Check file permissions (755/644)
- [ ] Check file encoding (UTF-8 without BOM)
- [ ] Test with minimal `index-test.php`

---

## 🆘 If Nothing Works

1. **Contact hosting support** with:
   - Error log contents
   - PHP version
   - What you've tried

2. **Try different hosting:**
   - Some hosts have restrictions
   - Test on different server

3. **Check server requirements:**
   - PHP 7.4+
   - mod_rewrite enabled
   - Allow .htaccess files

---

## 📝 Files to Create/Test

1. `test-minimal.php` - Simplest PHP test
2. `test-syntax.php` - Test config.php loading
3. `index-test.php` - Minimal homepage test
4. `.htaccess.minimal` - Simplest .htaccess

---

## 🎯 Expected Results

**test-minimal.php:**
- Should show "PHP is working!" and phpinfo()
- If 500 error: PHP not executing (server issue)

**After disabling .htaccess:**
- Site should work (if .htaccess was the issue)
- If still 500: Code issue

**test-syntax.php:**
- Should show step-by-step progress
- Will show where it fails

