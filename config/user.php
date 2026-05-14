<?php

return [
    'redirects' => [
        'login' => '/',
        'logout' => '/',
    ],
    'permissions' => [
        // Any of these ACL permissions can unlock the admin menu link.
        'admin_menu' => ['admin.access', 'admin'],
    ],
    'layouts' => [
        'guest' => 'cms::layouts.default',
        'authenticated' => 'cms::layouts.app',
    ],
];
