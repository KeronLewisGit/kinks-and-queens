<?php

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * One-time installer for hosts without SSH. Visit /setup.php?token=YOUR_SETUP_TOKEN
 * after uploading the app and filling in its .env. Deletes itself when done.
 *
 * Works with both layouts: the zip package (app in kq-app/, public files in the web root)
 * and a Git deployment (repository cloned into kq-src/, public files in kq-src/public).
 */
$gitLayout = is_dir(__DIR__.'/kq-src/app');
$appPath = $gitLayout ? __DIR__.'/kq-src' : __DIR__.'/kq-app';
$publicPath = $gitLayout ? $appPath.'/public' : __DIR__;
$appFolder = basename($appPath);
$envFile = $appPath.'/.env';
header('Content-Type: text/html; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow');
echo '<!doctype html><meta name="viewport" content="width=device-width"><title>Kinks &amp; Queens setup</title><body style="font-family:system-ui;max-width:640px;margin:40px auto;padding:0 16px;line-height:1.5;background:#f8f2e9;color:#2b1a11">';
echo '<h1>Kinks &amp; Queens installer</h1>';

if (! file_exists($envFile)) {
    exit('<p style="color:#a4473a">'.$appFolder.'/.env not found. Copy '.$appFolder.'/.env.example to '.$appFolder.'/.env and fill in the database details first.</p>');
}
$env = [];
foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    $line = trim($line);
    if ($line === '' || $line[0] === '#' || ! str_contains($line, '=')) {
        continue;
    }
    [$k, $v] = explode('=', $line, 2);
    $env[trim($k)] = trim(trim($v), "\"'");
}
$token = $env['SETUP_TOKEN'] ?? '';
$given = $_GET['token'] ?? '';
if (strlen($token) < 32 || ! is_string($given) || ! hash_equals($token, $given)) {
    http_response_code(403);
    exit('<p style="color:#a4473a">Invalid or missing token. Set SETUP_TOKEN in .env to a random value of at least 32 characters and open /setup.php?token=THAT_VALUE.</p>');
}

require $appPath.'/vendor/autoload.php';
$app = require $appPath.'/bootstrap/app.php';
$app->usePublicPath($publicPath);
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

// Once an owner exists the site is installed: this file never runs again, even if an upgrade zip restores it.
try {
    $installed = Schema::hasTable('users') && User::where('role', 'owner')->exists();
} catch (Throwable) {
    $installed = false;
}
if ($installed) {
    @unlink(__FILE__);
    http_response_code(410);
    exit('<p>Kinks &amp; Queens is already installed. This installer has been switched off.</p>');
}
$run = function (string $cmd, array $args = []) use ($kernel) {
    $out = new BufferedOutput;
    $code = $kernel->call($cmd, $args, $out);
    echo '<pre style="background:#fff;padding:8px;font-size:12px;white-space:pre-wrap">$ php artisan '.htmlspecialchars($cmd).'  → '.($code === 0 ? 'OK' : 'FAILED')."\n".htmlspecialchars($out->fetch()).'</pre>';

    return $code === 0;
};

$ok = true;
if (empty($env['APP_KEY'])) {
    $ok = $run('key:generate', ['--force' => true]) && $ok;
}
$ok = $run('migrate', ['--force' => true]) && $ok;
$ok = $run('db:seed', ['--force' => true]) && $ok;
$run('optimize');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $ok) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    if (filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($pass) >= 10) {
        $run('app:create-admin', ['--name' => $name ?: 'Owner', '--email' => $email, '--password' => $pass]);
        $removed = @unlink(__FILE__);
        echo '<p style="color:#2f6b3a"><strong>Done.</strong> Owner account created. <a href="/admin/login">Sign in</a>, then delete the SETUP_TOKEN line from .env.</p>';
        if (! $removed) {
            echo '<p style="color:#a4473a;font-weight:bold">setup.php could not delete itself. Delete public_html/setup.php now in File Manager.</p>';
        }
        exit;
    }
    echo '<p style="color:#a4473a">Please enter a valid email and a password of at least 10 characters.</p>';
}
echo '<h2>Create the owner account</h2><form method="post"><p><input name="name" placeholder="Name" required style="width:100%;padding:10px"></p><p><input name="email" type="email" placeholder="Email" required style="width:100%;padding:10px"></p><p><input name="password" type="password" placeholder="Password (10+ characters)" required minlength="10" style="width:100%;padding:10px"></p><button style="padding:12px 20px;background:#2b1a11;color:#f8f2e9;border:0;border-radius:99px">Create owner &amp; finish</button></form>';
