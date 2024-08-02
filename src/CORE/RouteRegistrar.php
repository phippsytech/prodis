<?php

namespace NDISmate;

use Slim\App;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionException;

/*
 * Example usage
 *
 * // Phippsy - see this chat for more info: https://chatgpt.com/c/542c69c4-a507-4306-9dbe-85d5155f9aa3
 *
 * use NDISmate\RouteRegistrar;
 *
 * $app = AppFactory::create();
 *
 * // Create RouteRegistrar instance and register routes
 * $routeRegistrar = new RouteRegistrar($app);
 * $routeRegistrar->registerRoutesFromDirectory(__DIR__ . '/../src');
 *
 * // Run app
 * $app->run();
 */

class RouteRegistrar
{
    private $app;
    private $namespace;

    public function __construct(App $app, string $namespace = 'NDISmate\\')
    {
        $this->app = $app;
        $this->namespace = $namespace;
    }

    public function registerRoutesFromDirectory(string $directory)
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getFilename() === 'Controller.php') {
                $this->registerRouteFromFile($file->getPathname());
            }
        }
    }

    private function registerRouteFromFile(string $filePath)
    {
        $relativePath = str_replace([realpath(__DIR__ . '/../'), '/', '.php'], ['', '\\', ''], realpath($filePath));
        $className = $this->namespace . $relativePath;

        if (class_exists($className)) {
            try {
                $reflection = new ReflectionClass($className);

                if ($reflection->isSubclassOf('NDISmate\CORE\BaseController')) {
                    $route = $this->getRouteFromClassName($relativePath);
                    $this->app->post($route, [$className, 'handle']);
                }
            } catch (ReflectionException $e) {
                // Handle the exception if needed
            }
        }
    }

    private function getRouteFromClassName(string $className): string
    {
        $route = str_replace(['NDISmate\\', '\Controller'], '', $className);
        return '/' . strtolower(str_replace('\\', '/', $route));
    }
}
