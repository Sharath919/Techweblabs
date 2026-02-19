# Fix File Permissions Error

## Error Message
```
SoftException in Application.cpp:355: UID of script is smaller than min_uid
```

This error occurs when files have incorrect ownership on CloudLinux/cPanel servers.

## Solution

### Option 1: Fix via cPanel File Manager (Recommended)

1. **Login to cPanel**
2. **Go to File Manager**
3. **Navigate to `/public_html/admin/` folder**
4. **Select all files** in the admin folder (Ctrl+A or Cmd+A)
5. **Right-click → Change Permissions**
6. **Set permissions to:**
   - Files: `644`
   - Folders: `755`
7. **Click Change Permissions**

### Option 2: Fix via SSH/Terminal

If you have SSH access:

```bash
# Navigate to your web directory
cd /home/techweb/public_html

# Fix ownership (replace 'techweb' with your cPanel username if different)
chown -R techweb:techweb admin/

# Fix file permissions
find admin/ -type f -exec chmod 644 {} \;
find admin/ -type d -exec chmod 755 {} \;

# Make PHP files executable if needed
find admin/ -name "*.php" -exec chmod 644 {} \;
```

### Option 3: Fix via cPanel Terminal

1. **Login to cPanel**
2. **Go to Terminal/SSH Access**
3. **Run these commands:**

```bash
cd public_html
chown -R $USER:$USER admin/
chmod -R 755 admin/
find admin/ -type f -exec chmod 644 {} \;
```

## Verify Fix

After fixing permissions:

1. **Check file ownership:**
   ```bash
   ls -la admin/login.php
   ```
   Should show: `techweb techweb` (or your username)

2. **Check permissions:**
   ```bash
   ls -l admin/login.php
   ```
   Should show: `-rw-r--r--` (644)

3. **Test the login page:**
   - Visit: `https://techweblabs.com/admin/login.php`
   - Should load without errors

## Additional Notes

- **Never use root ownership** for web files on shared hosting
- **Files should be owned by your cPanel user** (usually your domain name or username)
- **Folders need execute permission (755)** to be accessible
- **Files need read permission (644)** to be readable by the web server

## If Still Not Working

1. **Check if the file is actually owned by the correct user:**
   ```bash
   whoami
   ls -la admin/login.php
   ```

2. **Contact your hosting provider** if you cannot change file ownership

3. **Check if your hosting uses CageFS** - you may need to adjust settings in cPanel
