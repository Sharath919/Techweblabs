# Fix File Permissions for AI Article Generator

## Error: "UID of script is smaller than min_uid"

This is the same file ownership/permission issue as before. Follow these steps:

### Option 1: Using cPanel File Manager

1. Login to cPanel
2. Go to File Manager
3. Navigate to `/public_html/admin/`
4. Right-click on `ai-article-generator.php` and select "Change Permissions"
5. Set permissions to **644** for files and **755** for directories
6. Click "Change Permissions Recursively" if needed

### Option 2: Using SSH (Recommended)

```bash
# Navigate to your public_html directory
cd /home/techweb/public_html

# Fix ownership (replace 'techweb' with your cPanel username if different)
sudo chown -R techweb:techweb admin/

# Fix file permissions
find admin/ -type f -exec chmod 644 {} \;
find admin/ -type d -exec chmod 755 {} \;

# Specifically for the new AI files
chmod 644 admin/ai-article-generator.php
chmod 644 admin/includes/ai-helper.php
chmod 644 admin/config/ai-config.php
```

### Important: Secure Your API Key

⚠️ **SECURITY WARNING**: Your OpenAI API key is visible in `admin/config/ai-config.php`. 

1. **Add to .gitignore** (if using Git):
   ```
   admin/config/ai-config.php
   ```

2. **Set proper permissions**:
   ```bash
   chmod 600 admin/config/ai-config.php  # Only owner can read/write
   ```

3. **Alternative**: Use environment variables instead (more secure):
   ```bash
   export OPENAI_API_KEY='your-api-key-here'
   ```
   
   Then in `ai-config.php`, the `getAIAPIKey()` function will automatically use the environment variable.

### Verify Fix

After fixing permissions, try accessing:
- https://techweblabs.com/admin/ai-article-generator.php

If you still see the error, contact your hosting provider or check your CloudLinux/cPanel configuration.
