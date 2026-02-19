# Blog Admin Panel - Setup Guide

## Overview
A secure, SEO-optimized blog management system built with PHP and MySQL. The admin panel allows you to create, edit, and manage blog posts with comprehensive SEO features.

## Features
- ✅ Secure authentication system
- ✅ Full CRUD operations for blog posts
- ✅ SEO-optimized URLs and metadata
- ✅ Category and tag management
- ✅ Meta tags (title, description, keywords)
- ✅ Open Graph support
- ✅ Schema.org structured data
- ✅ Reading time calculation
- ✅ View tracking
- ✅ Draft/Published/Archived status

## Installation

### Step 1: Database Setup

1. Create a MySQL database:
```sql
CREATE DATABASE techweblabs_blog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Import the database schema:
```bash
mysql -u root -p techweblabs_blog < admin/database.sql
```

Or via phpMyAdmin:
- Go to phpMyAdmin
- Select your database
- Click "Import"
- Choose `admin/database.sql`

### Step 2: Configure Database Connection

Edit `admin/config/db_config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');          // Your MySQL username
define('DB_PASS', '');              // Your MySQL password
define('DB_NAME', 'techweblabs_blog'); // Your database name
```

### Step 3: Access Admin Panel

1. Navigate to: `http://your-domain.com/admin/login.php`

2. Default credentials:
   - **Username:** admin
   - **Password:** admin123
   
   ⚠️ **IMPORTANT:** Change the default password immediately after first login!

3. To change password, update the database:
```sql
UPDATE admin_users 
SET password_hash = '$2y$10$YOUR_HASHED_PASSWORD' 
WHERE username = 'admin';
```

To generate a new password hash:
```php
<?php
echo password_hash('your-new-password', PASSWORD_DEFAULT);
?>
```

## File Structure

```
admin/
├── config/
│   ├── db_config.php      # Database configuration
│   └── auth.php           # Authentication functions
├── assets/
│   ├── css/
│   │   └── admin.css      # Admin panel styles
│   └── js/
│       └── admin.js       # Admin panel JavaScript
├── includes/
│   ├── header.php         # Admin header
│   └── sidebar.php        # Admin sidebar navigation
├── database.sql           # Database schema
├── login.php              # Login page
├── index.php              # Dashboard
├── posts.php              # Posts list
├── post-new.php           # Create new post
├── post-edit.php          # Edit existing post
├── post-delete.php        # Delete post
└── logout.php             # Logout handler
```

## Usage

### Creating a Blog Post

1. Login to admin panel
2. Go to **Posts** → **New Post**
3. Fill in:
   - **Title:** Post title
   - **Slug:** URL-friendly version (auto-generated from title)
   - **Content:** Full blog post content
   - **Excerpt:** Short summary
   - **SEO Settings:**
     - Meta Title (50-60 chars recommended)
     - Meta Description (150-160 chars recommended)
     - Meta Keywords
     - Focus Keyword
   - **Images:** Featured image and Open Graph image
   - **Categories & Tags**
4. Set status (Draft/Published/Archived)
5. Click **Save Post**

### SEO Features

- **Auto-generated slugs:** SEO-friendly URLs from post titles
- **Unique slugs:** Automatically ensures no duplicate URLs
- **Meta tags:** Full control over title, description, keywords
- **Schema markup:** Structured data for search engines
- **Open Graph:** Social media sharing optimization
- **Reading time:** Automatically calculated based on content length

## Blog URL Structure

- Blog listing: `/blogs` or `/blog`
- Individual post: `/blogs/your-post-slug`

Example: `https://techweblabs.com/blogs/mobile-app-development-guide`

## Security Features

- ✅ Prepared statements (SQL injection prevention)
- ✅ Password hashing (bcrypt)
- ✅ Session security (httponly, secure cookies)
- ✅ Input sanitization
- ✅ XSS protection
- ✅ CSRF protection ready (can be added)

## Frontend Integration

The blog posts will be accessible at:
- `/blogs/{slug}` - Individual blog post
- `/blogs` - Blog listing page

To create the frontend pages, you'll need to create:
1. `pages/blogs/post.php` - Single post template
2. `pages/blogs/index.php` - Blog listing template

These will be created next, or you can reference the database directly to build them.

## Database Schema

### Tables:
- `admin_users` - Admin user accounts
- `blog_posts` - Blog posts with SEO fields
- `blog_categories` - Post categories
- `blog_tags` - Post tags
- `blog_post_categories` - Post-Category relationships
- `blog_post_tags` - Post-Tag relationships

## Troubleshooting

### Cannot connect to database
- Check `db_config.php` credentials
- Ensure MySQL is running
- Verify database exists

### 404 on blog URLs
- Ensure `.htaccess` rules are enabled (Apache)
- If using PHP built-in server, use `router.php`
- Check router.php has blog route handling

### Login not working
- Check database connection
- Verify admin user exists in `admin_users` table
- Check password hash matches

## Next Steps

1. ✅ Set up database
2. ✅ Configure database connection
3. ✅ Change default admin password
4. ⏳ Create frontend blog pages (next step)
5. ⏳ Customize admin panel styling
6. ⏳ Add image upload functionality

## Support

For issues or questions, check:
- Database connection settings
- File permissions
- PHP error logs
- Browser console for JavaScript errors
