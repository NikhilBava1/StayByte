<?php
$files = scandir('.'); // Get all files and directories in the current folder

foreach ($files as $file) {
    // Skip . and ..
    if ($file === '.' || $file === '..') continue;

    // Skip if it's a directory
    if (is_dir($file)) continue;

    $info = pathinfo($file);
    $filename = $info['filename'];

    // Only rename if it's not already .php
    if (isset($info['extension']) && strtolower($info['extension']) !== 'php') {
        $newName = $filename . '.php';
        rename($file, $newName);
        echo "Renamed: $file -> $newName\n";
    }
}
?>
