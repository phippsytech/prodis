<?php

// function logAutoloader(string $message): void
// {
//     // file_put_contents(__DIR__ . '/autoloader.log', $message . PHP_EOL, FILE_APPEND);
// }

spl_autoload_register(function (string $class): void {
    static $fileCache = [];

    $prefix = 'NDISmate\\';
    $baseDir = __DIR__ . '/src/';
    $len = strlen($prefix);

    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    // logAutoloader("Trying to load class: $class => $file");

    if (isset($fileCache[$file])) {
        if ($fileCache[$file]) {
            // logAutoloader("File exists in cache, loading: $file");
            require $file;
        }
        // else {
        // logAutoloader("Failed to load class from cache: $class");
        // }
        return;
    }

    if (file_exists($file)) {
        // logAutoloader("File exists, loading: $file");
        $fileCache[$file] = true;
        require $file;
        return;
    }

    $pathParts = explode('/', $relativeClass);
    if (count($pathParts) === 1) {
        $classFolder = str_replace('\\', '/', $relativeClass);
        $folderClassFile = $baseDir . $classFolder . '/' . basename($classFolder) . '.php';
        // logAutoloader("Trying to load folder-named class: $class => $folderClassFile");

        if (file_exists($folderClassFile)) {
            // logAutoloader("File exists, loading: $folderClassFile");
            $fileCache[$folderClassFile] = true;
            require $folderClassFile;
            return;
        }

        $fileCache[$folderClassFile] = false;
    }

    $fileCache[$file] = false;
    // logAutoloader("Failed to load class: $class");
});
