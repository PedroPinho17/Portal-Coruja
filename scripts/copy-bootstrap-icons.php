<?php
// Copy bootstrap-icons assets to public/plugins/bootstrap-icons
$base = __DIR__ . '/../';
$vendor = $base . 'vendor/twbs/bootstrap-icons';
$target = $base . 'public/plugins/bootstrap-icons';

$files = [
    $vendor . '/font/bootstrap-icons.css' => $target . '/bootstrap-icons.css',
    $vendor . '/font/bootstrap-icons.min.css' => $target . '/bootstrap-icons.min.css',
    $vendor . '/font/fonts/bootstrap-icons.woff' => $target . '/fonts/bootstrap-icons.woff',
    $vendor . '/font/fonts/bootstrap-icons.woff2' => $target . '/fonts/bootstrap-icons.woff2',
];

$errors = [];
foreach ($files as $src => $dest) {
    if (!is_file($src)) { $errors[] = "Missing: $src"; continue; }
    $dir = dirname($dest);
    if (!is_dir($dir)) { mkdir($dir, 0775, true); }
    // Adjust font paths inside CSS after copy
    if (substr($src, -4) === '.css') {
        $css = file_get_contents($src);
        // Replace relative font path to point to ./fonts/
        $css = preg_replace('/url\((?:\"|\')?fonts\//','url("fonts/', $css);
        // Write transformed css to destination
        file_put_contents($dest, $css);
        continue;
    }
    if (!copy($src, $dest)) { $errors[] = "Failed to copy $src"; }
}

if ($errors) {
    fwrite(STDERR, "Errors copying bootstrap-icons:\n" . implode("\n", $errors) . "\n");
    exit(1);
}

echo "Bootstrap Icons copied to public/plugins/bootstrap-icons.\n";
