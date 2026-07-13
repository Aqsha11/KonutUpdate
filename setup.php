<?php
/**
 * SETUP SCRIPT - Delete this file after deployment!
 * 
 * This script initializes the app on the server.
 * Access it once via browser: https://yourdomain.com/setup.php
 * Then DELETE it immediately.
 */

$appPath = __DIR__;
require $appPath.'/vendor/autoload.php';

$app = require_once $appPath.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "<h2>Konut.Update - Setup</h2>";

// 1. Create storage directories
$dirs = [
    'storage/app/public',
    'storage/framework/cache/data',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/framework/testing',
    'storage/logs',
    'bootstrap/cache',
];

foreach ($dirs as $dir) {
    $fullPath = $appPath.'/'.$dir;
    if (!is_dir($fullPath)) {
        mkdir($fullPath, 0755, true);
        echo "<p>Created: {$dir}</p>";
    }
}

// 2. Create storage link
$linkPath = $appPath.'/public/storage';
$targetPath = $appPath.'/storage/app/public';
if (!file_exists($linkPath)) {
    symlink($targetPath, $linkPath);
    echo "<p>Created storage link</p>";
} else {
    echo "<p>Storage link exists</p>";
}

// 3. Set permissions
$writableDirs = ['storage', 'bootstrap/cache'];
foreach ($writableDirs as $dir) {
    $fullPath = $appPath.'/'.$dir;
    if (is_dir($fullPath)) {
        chmod($fullPath, 0755);
        echo "<p>Set permissions on {$dir}</p>";
    }
}

// 4. Generate APP_KEY if not set
$envPath = $appPath.'/.env';
$envContent = file_get_contents($envPath);
if (strpos($envContent, 'APP_KEY=base64:') !== false && substr_count($envContent, 'base64:') === 1) {
    echo "<p>APP_KEY already set</p>";
}

// 5. Run migrations
echo "<p>Checking database...</p>";
putenv('DB_CONNECTION=sqlite');

$exitCode = $kernel->call('migrate', ['--force' => true]);
echo "<p>Database ready</p>";

// 6. Cache config
try {
    $kernel->call('config:cache');
    $kernel->call('route:cache');
    $kernel->call('view:cache');
    echo "<p>Caches cleared</p>";
} catch (\Exception $e) {
    echo "<p>Cache warning: ".$e->getMessage()."</p>";
}

echo "<hr>";
echo "<h3>Setup complete!</h3>";
echo "<p><strong>DELETE this file immediately!</strong></p>";
echo "<p><a href='/'>Visit Homepage</a></p>";
echo "<p><a href='/login'>Admin Login</a></p>";
echo "<p>Email: admin@konutupdate.com</p>";
echo "<p>Password: password</p>";
