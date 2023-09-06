<?php

return [
    // Panel Credential
    'username'          => env('PURE_USERNAME', 'administrator'),
    'password'          => env('PURE_PASSWORD', '12345678'),

    // JWT Settings
    'jwt_secret'        => env('JWT_SECRET', env('APP_KEY')),
    'jwt_access'        => env('JWT_ACCESS', 900),
    'jwt_refresh'       => env('JWT_REFRESH', 7200),

    // Custom Vars
    'name'              => env('PURE_NAME', 'Pure Control Panel'),
    'website'           => env('PURE_WEBSITE', 'https://visiosoft.com.tr'),
//    'activesetupcount'  => env('PURE_ACTIVESETUPCOUNT', 'https://service.visiosoft.com.tr/setupcount'),
//    'documentation'     => env('PURE_DOCUMENTATION', 'https://visiosoft.com.tr/docs.html'),
//    'app'               => env('PURE_APP', 'https://play.google.com/store/apps/details?id=it.christiangiupponi.pure'),

    // Global Settings
    'users_prefix'      => env('PURE_USERS_PREFIX', 'cp'),
    'phpvers'           => ['8.1','8.0','7.4'],
    'services'          => ['nginx','php','mysql','redis','supervisor'],
    'default_php'       => '8.0',
];
