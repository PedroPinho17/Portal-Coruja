<?php
// copies necessary bootstrap-fileinput assets from vendor to public/plugins/krajee-fileinput
// Run: php scripts/copy-fileinput.php

$base = __DIR__ . '/../';
$vendorPath = $base . 'vendor/kartik-v/bootstrap-fileinput';
$publicPath = $base . 'public/plugins/krajee-fileinput';

$map = [
    $vendorPath . '/css/fileinput.min.css' => $publicPath . '/css/fileinput.min.css',
    $vendorPath . '/js/fileinput.min.js' => $publicPath . '/js/fileinput.min.js',
    $vendorPath . '/js/plugins/piexif.min.js' => $publicPath . '/js/plugins/piexif.min.js',
    $vendorPath . '/themes/bs5/theme.min.js' => $publicPath . '/themes/bs5/theme.min.js',
];

$errors = [];
foreach ($map as $src => $dest) {
    if (!is_file($src)) {
        $errors[] = "Missing source: $src";
        continue;
    }
    $destDir = dirname($dest);
    if (!is_dir($destDir)) {
        mkdir($destDir, 0775, true);
    }
    if (!copy($src, $dest)) {
        $errors[] = "Failed to copy $src";
    } else {
        echo "Copied: $src -> $dest\n";
    }
}

if ($errors) {
    fwrite(STDERR, "Errors encountered:\n" . implode("\n", $errors) . "\n");
    exit(1);
}

echo "All fileinput assets copied successfully.\n";
