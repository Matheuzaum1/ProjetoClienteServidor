<?php

return [

    'paths' => ['up', 'usuarios', 'usuarios/*', 'sanctum/csrf-cookie', 'token/refresh'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['http://127.0.0.1:8888', 'http://localhost:8888'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];