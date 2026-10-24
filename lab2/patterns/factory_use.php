<?php
declare(strict_types=1);

spl_autoload_register(function (string $class): void {
    $prefixes = [
        'Factory\\' => __DIR__ . '/factory/',
        'MVC\\'     => __DIR__ . '/mvc/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) === 0) {
            $relativeClass = substr($class, $len);
            
            $file = $baseDir . strtolower(str_replace('\\', '/', $relativeClass)) . '.php';
            
            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
});

use Factory\Router;

$obj = Router::parse('users');
echo $obj->render() . "\n";
