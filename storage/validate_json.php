<?php

$files = ['ar.json', 'en.json', 'fr.json'];
foreach ($files as $file) {
    $path = __DIR__ . '/../lang/' . $file;
    if (!file_exists($path)) {
        echo "File not found: $file" . PHP_EOL;
        continue;
    }
    
    $content = file_get_contents($path);
    $data = json_decode($content, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "Error in $file: " . json_last_error_msg() . PHP_EOL;
    } else {
        echo "$file is valid JSON." . PHP_EOL;
    }
}
