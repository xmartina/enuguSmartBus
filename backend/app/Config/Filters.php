<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;

class Filters extends BaseConfig
{
    public $aliases = [
        'csrf'      => CSRF::class,
        'toolbar'   => DebugToolbar::class,
        'honeypot'  => Honeypot::class,
        'cors'      => \Fluent\Cors\Filters\CorsFilter::class,
        'authGuard' => \App\Filters\AuthGuard::class,
    ];

    public $globals = [
        'before' => [
            'honeypot',
            'csrf' => [
                'except' => [
                    // ✅ Exclude Paystack & API routes from CSRF
                    'backend/modules/api/v1/paymethods/paystack/callback',
                    'modules/api/v1/paymethods/paystack/webhook',
                    'modules/api/v1/*',

                    // Other AJAX exclusions
                    'ajax/vehicle/pic/delete',
                    'modules/backend/tickets/seatlayout',
                    'modules/backend/websettings/factory-reset'
                ]
            ],
        ],
        'after' => [
            'toolbar',
            'honeypot',
        ],
    ];

    public $methods = [];

    public $filters = [
        // ✅ Apply CORS to API routes
        'cors' => [
            'after' => ['modules/api/v1/*']
        ],

        // ✅ Only backend dashboard is protected
        'authGuard' => [
            'before' => [
                'modules/backend/*',
            ]
        ],
    ];
}
