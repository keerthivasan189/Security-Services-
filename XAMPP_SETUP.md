# XAMPP Setup — Required Steps

## Step 1: Enable mod_rewrite in Apache

Open: `C:\xampp\apache\conf\httpd.conf`

Find this line and REMOVE the `#`:
```
#LoadModule rewrite_module modules/mod_rewrite.so
```
Change to:
```
LoadModule rewrite_module modules/mod_rewrite.so
```

## Step 2: Allow .htaccess overrides

In the SAME file `httpd.conf`, find the section for `htdocs`:
```apache
<Directory "C:/xampp/htdocs">
    Options Indexes FollowSymLinks Includes ExecCGI
    AllowOverride None       <-- CHANGE THIS
    Require all granted
</Directory>
```
Change `AllowOverride None` to `AllowOverride All`:
```apache
<Directory "C:/xampp/htdocs">
    Options Indexes FollowSymLinks Includes ExecCGI
    AllowOverride All
    Require all granted
</Directory>
```

## Step 3: Restart Apache

In XAMPP Control Panel → click **Stop** then **Start** next to Apache.

## Step 4: Set correct BASE_URL

Open `config/db.php` — check DB credentials match your XAMPP MySQL.

Open `index.php` line 4 — make sure it reads:
```php
define('BASE_URL', 'http://localhost/hrms');
```

## Step 5: Import Database

Open phpMyAdmin → http://localhost/phpmyadmin
→ Click "Import" → Choose file: `hrms/config/install.sql` → Click Go

## Step 6: Test Login

Open: http://localhost/hrms
- Username: `admin`
- Password: `password`

## Quick Test URLs

| URL | Should show |
|-----|------------|
| http://localhost/hrms | Login page |
| http://localhost/hrms/dashboard/index | Dashboard |
| http://localhost/hrms/employees/index | Employee list |
| http://localhost/hrms/clients/index | Client list |
| http://localhost/hrms/attendance/bulk | Attendance entry |
