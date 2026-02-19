# How to Start the PHP Development Server

## Important: Use the Router File

The PHP built-in server **does NOT process `.htaccess` files**. You must use the `router.php` file to handle URL routing.

## Start Command

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/techweblabs_live
php -S localhost:8000 router.php
```

## Why router.php is Required

- `.htaccess` rewrite rules only work with Apache
- PHP built-in server needs `router.php` to route URLs like `/pharmeasy-clone` to `/pages/ondemand/pharmeasy-clone.php`
- Without `router.php`, all URLs will show the homepage

## Access Your Site

- Homepage: http://localhost:8000/
- Service Pages: http://localhost:8000/pharmeasy-clone
- About: http://localhost:8000/about
- Contact: http://localhost:8000/contact

## Troubleshooting

If pages still show the homepage:
1. Make sure you're using `router.php` in the command
2. Stop the server (Ctrl+C) and restart with the correct command
3. Clear your browser cache
4. Try a hard refresh (Ctrl+Shift+R or Cmd+Shift+R)
