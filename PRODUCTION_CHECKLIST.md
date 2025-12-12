# QR Code Display Issue - Production Checklist

## Issue
QR codes don't display on admin/qr-codes/{id} but work on admin/qr-codes/{id}/print in production.

## Steps to Fix

### 1. Create Storage Symlink
```bash
php artisan storage:link
```

### 2. Check File Permissions
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### 3. Verify Storage Directory
Check that these directories exist and are writable:
- `storage/app/public/qrcodes/`
- `public/storage/` (symlink to storage/app/public)

### 4. Test File Path
In your production environment, add this temporarily to debug:
```php
// In show.blade.php, add after line 13:
@php
    if($qrCode->qr_image_path) {
        $fullPath = storage_path('app/public/' . $qrCode->qr_image_path);
        $publicPath = public_path('storage/' . $qrCode->qr_image_path);
        dump([
            'qr_image_path' => $qrCode->qr_image_path,
            'storage_path' => $fullPath,
            'exists_storage' => file_exists($fullPath),
            'public_path' => $publicPath,
            'exists_public' => file_exists($publicPath),
            'asset_url' => asset('storage/' . $qrCode->qr_image_path)
        ]);
    }
@endphp
```

### 5. Web Server Configuration
Ensure your web server (Apache/Nginx) serves the `/storage` directory.

**Nginx example:**
```nginx
location /storage {
    alias /path/to/your/app/public/storage;
}
```

**Apache:** Should work automatically if .htaccess is properly configured.
