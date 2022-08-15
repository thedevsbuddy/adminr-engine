<?php

use Devsbuddy\AdminrEngine\Models\AdminrResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;


if (!function_exists('coreUiIcon')) {
    function coreUiIcon($id): string
    {
        return asset('vendor/adminr-engine/coreui/free.svg#' . $id);
    }
}

if (!function_exists('activeRoutes')) {
    function activeRoutes($routes): bool
    {
        return in_array(Route::currentRouteName(), $routes);
    }
}

if (!function_exists('activeRoute')) {
    function activeRoute($route): bool
    {
        return Route::currentRouteName() == $route;
    }
}


if (!function_exists('returnIfRoute')) {
    function returnIfRoute($route, $return, $fallback = null): mixed
    {
        if (Route::currentRouteName() == $route) {
            return $return;
        } else {
            return $fallback;
        }
    }
}

if (!function_exists('returnIfRoutes')) {
    function returnIfRoutes($routes, $return, $fallback = null)
    {
        if (in_array(Route::currentRouteName(), $routes)) {
            return $return;
        } else {
            return $fallback;
        }
    }
}

if (!function_exists('getVersion')) {
    function getVersion(string $prefix = null): string
    {
        $file = \Illuminate\Support\Facades\File::get(base_path() . '/composer.json');
        return $prefix . '' . json_decode($file)->version;
    }
}


if (!function_exists('getRouteMiddlewares')) {
    function getRouteMiddlewares(string $method, string $resourceName = null): ?array
    {
        Cache::forget(Str::camel($resourceName . $method . 'Middleware'));
        if (!app()->runningInConsole() && AdminrResource::where('name', Str::title($resourceName))->exists()) {
            return Cache::remember(
                key: Str::camel($resourceName . $method . 'Middleware'),
                ttl: config('adminr.cache_remember_time'),
                callback: fn () => AdminrResource::where('name', Str::title($resourceName))->value('api_route_middlewares')->{Str::camel($method)}
            );
        } else {
            return null;
        }
    }
}
