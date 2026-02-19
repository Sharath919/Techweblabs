# Quick Fix for 500 Error on Server

## 🚨 IMMEDIATE STEPS

### Step 1: Upload test-server.php
Upload `test-server.php` to your `public_html` folder and access it:
```
https://yourdomain.com/test-server.php
```

This will show you exactly what's wrong.

---

### Step 2: Fix config.php (Already Updated)
The `config.php` file has been updated with smart path detection. Make sure you upload the new version.

---

### Step 3: Check .htaccess
If `.htaccess` is causing issues, temporarily rename it:
```bash
mv .htaccess .htaccess.backup
```

Then test if the site works. If it works, use the simplified version:
```bash
cp .htaccess.simple .htaccess
```

---

### Step 4: Common Issues & Fixes

#### Issue 1: ROOT_DIR Path Wrong
**Symptom:** Test shows homepage folder not found

**Fix:** The updated `config.php` should handle this automatically. If not, manually set in `config.php`:
```php
define('ROOT_DIR', '/home/username/public_html/');  // Replace with your actual path
```

#### Issue 2: Missing Files
**Symptom:** Test shows files/directories missing

**Fix:** Upload all files:
- homepage/ folder
- pages/ folder
- images/ folder
- css/ folder
- js/ folder
- includes/ folder

#### Issue 3: File Permissions
**Symptom:** Files not readable

**Fix:** Set permissions via cPanel File Manager or SSH:
```bash
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
```

#### Issue 4: PHP Version
**Symptom:** PHP version too old

**Fix:** Contact hosting to upgrade to PHP 7.4+ or use cPanel to select PHP version.

---

## ✅ VERIFICATION CHECKLIST

After fixes, verify:
- [ ] `test-server.php` shows all green checkmarks
- [ ] Homepage loads: `https://yourdomain.com/`
- [ ] Service page loads: `https://yourdomain.com/swiggy-clone`
- [ ] No 500 errors
- [ ] Images load
- [ ] CSS/JS load

---

## 🆘 IF STILL NOT WORKING

1. **Check server error logs** (cPanel → Error Logs)
2. **Enable error display** temporarily:
   ```php
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```
3. **Contact hosting support** with:
   - Error log contents
   - PHP version
   - test-server.php results

---

## 📝 FILES TO UPLOAD

Make sure these are uploaded:
- ✅ config.php (updated version)
- ✅ index.php (updated version)
- ✅ .htaccess or .htaccess.simple
- ✅ homepage/ folder
- ✅ pages/ folder
- ✅ All other folders and files

---

## 🔒 SECURITY NOTE

**Delete `test-server.php` after testing!** It shows sensitive server information.

