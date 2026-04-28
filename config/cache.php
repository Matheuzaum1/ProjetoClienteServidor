<?php

use Illuminate\Support\Str;

return [

    'default' => env('CACHE_STORE', env('CACHE_DRIVER', 'file')),

    'stores' => [

        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],

        'database' => [
            'driver' => 'database',
            'table' => env('CACHE_TABLE', 'cache'),
            'connection' => env('CACHE_CONNECTION'),
            'lock_connection' => env('CACHE_LOCK_CONNECTION'),
            'lock_table' => env('CACHE_LOCK_TABLE', 'cache_locks'),
        ],

        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
            'lock_path' => storage_path('framework/cache/data'),
        ],

        'null' => [
            'driver' => 'null',
        ],

    ],

    'prefix' => env(
        'CACHE_PREFIX',
        Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_'
    ),

];