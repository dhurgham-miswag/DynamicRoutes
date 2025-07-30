<?php

namespace App\Services;

use App\Models\ApiRoute;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class DynamicRouteService
{
    public function registerRoutes(): void
    {
        $routes = ApiRoute::query()->get();
        \Log::info("Registering " . $routes->count() . " routes");

        foreach ($routes as $route) {
            $this->registerRoute($route);
        }
    }

    protected function registerRoute(ApiRoute $route): void
    {
        $method = strtolower($route->http_method);
        $path = "{$route->service_group}/{$route->route_name}";
        $middleware = $route->middleware ?? 'api';
        $controller_class = "App\\Http\\Controllers\\" . ucfirst($middleware) . "\\" . ucfirst($route->service_group) . "\\{$route->controller_name}";
        
        \Log::info("Registering route: {$method} {$path} -> {$controller_class}");
        
        // Try multiple case variations if class doesn't exist
        $controller_name_parts = explode('\\', $controller_class);
        $last_part = end($controller_name_parts);
        
        $possible_classes = [
            $controller_class,
            str_replace(ucfirst($last_part), $last_part, $controller_class), // original case
            str_replace(ucfirst($last_part), strtolower($last_part), $controller_class), // lowercase
            str_replace(ucfirst($last_part), strtoupper($last_part), $controller_class), // uppercase
        ];
        
        \Log::info("Trying possible classes for route {$path}: " . implode(', ', $possible_classes));
        
        $found_class = null;
        foreach ($possible_classes as $class) {
            if (class_exists($class)) {
                $found_class = $class;
                \Log::info("Found existing class for route {$path}: {$class}");
                break;
            }
        }
        
        if (!$found_class) {
            Log::warning("Controller class not found for route {$path}. Tried: " . implode(', ', $possible_classes));
            return;
        }
        
        $controller_class = $found_class;

        if (!class_exists($controller_class)) {
            Log::warning("Controller class not found: {$controller_class}");
            return;
        }

        if (!$route->is_active) {
            \Log::info("Registering inactive route: {$method} {$path}");
            Route::$method($path, function () use ($route) {
                return response()->json([
                    'error' => 'Endpoint Unavailable',
                    'message' => 'This endpoint is currently offline or under maintenance.',
                    'status' => 'inactive'
                ], ResponseAlias::HTTP_SERVICE_UNAVAILABLE);
            });
            return;
        }

        \Log::info("Registering active route: {$method} {$path} -> {$controller_class}@{$route->method_name}");
        Route::$method($path, [$controller_class, $route->method_name]);
    }
}
