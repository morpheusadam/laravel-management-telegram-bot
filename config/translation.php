<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Package driver
    |--------------------------------------------------------------------------
    |
    | The package supports different drivers for translation management.
    |
    | Supported: "file", "database"
    |
    */
    'driver' => 'file',

    /*
    |--------------------------------------------------------------------------
    | Route group configuration
    |--------------------------------------------------------------------------
    |
    | The package ships with routes to handle language management. Update the
    | configuration here to configure the routes with your preferred group options.
    |
    */
    'route_group_config' => [
        'middleware' => ['web','auth'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Translation methods
    |--------------------------------------------------------------------------
    |
    | Update this array to tell the package which methods it should look for
    | when finding missing translations.
    |
    */
    'translation_methods' => ['trans', '__'],

    /*
    |--------------------------------------------------------------------------
    | Scan paths
    |--------------------------------------------------------------------------
    |
    | Update this array to tell the package which directories to scan when
    | looking for missing translations.
    |
    */
    'scan_paths' => [app_path(), resource_path()],

    /*
    |--------------------------------------------------------------------------
    | UI URL
    |--------------------------------------------------------------------------
    |
    | Define the URL used to access the language management too.
    |
    */
    'ui_url' => 'languages',

    /*
    |--------------------------------------------------------------------------
    | Database settings
    |--------------------------------------------------------------------------
    |
    | Define the settings for the database driver here.
    |
    */
    'database' => [

        'connection' => '',

        'languages_table' => 'languages',

        'translations_table' => 'translations',
    ],

    /* scan_mode = file , directory, copyFile, copyDirectory */
    'scan_groups'=> [   
        'custom-system'=>[
            '0'=> [
                'path'=>resource_path('views').DIRECTORY_SEPARATOR.'shared'.DIRECTORY_SEPARATOR.'variables.blade.php',
                'type'=>'view',
                'scan_mode'=>'file'
            ],
            '1'=> [
                'path'=>app_path().DIRECTORY_SEPARATOR.'Helpers'.DIRECTORY_SEPARATOR.'Custom.php',
                'type'=>'helper',
                'scan_mode'=>'file'
            ],         
            '2'=> [
                'path'=>app_path().DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR.'Home.php',
                'type'=>'controller',
                'scan_mode'=>'file'
            ],
            '3'=> [
                'path'=>app_path().DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR.'Auth',
                'type'=>'controller',
                'scan_mode'=>'directory'
            ],
            '4'=> [
                'path'=>app_path().DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR.'Webhook.php',
                'type'=>'controller',
                'scan_mode'=>'file'
            ],
            '5'=> [
                'path'=>resource_path('views').DIRECTORY_SEPARATOR.'auth',
                'type'=>'view',
                'scan_mode'=>'directory'
            ],
            '6'=> [
                'path'=>resource_path('views').DIRECTORY_SEPARATOR.'layouts'.DIRECTORY_SEPARATOR.'auth.blade.php',
                'type'=>'view',
                'scan_mode'=>'file'
            ],
            '7'=> [
                'path'=>resource_path('views').DIRECTORY_SEPARATOR.'layouts'.DIRECTORY_SEPARATOR.'error.blade.php',
                'type'=>'view',
                'scan_mode'=>'file'
            ],
            '8'=> [
                'path'=>resource_path('views').DIRECTORY_SEPARATOR.'shared'.DIRECTORY_SEPARATOR.'limit-check-error.blade.php',
                'type'=>'view',
                'scan_mode'=>'file'
            ],
            '9'=> [
                'path'=>app_path().DIRECTORY_SEPARATOR.'Services'.DIRECTORY_SEPARATOR.'Payment',
                'type'=>'services',
                'scan_mode'=>'directory'
            ]
        ],
        'custom-dashboard'=>[
            '0'=> [
                'path'=>app_path().DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR.'Dashboard.php',
                'type'=>'controller',
                'scan_mode'=>'file'
            ],
            '1'=> [
                'path'=>resource_path('views').DIRECTORY_SEPARATOR.'dashboard.blade.php',
                'type'=>'view',
                'scan_mode'=>'file'
            ]
        ],        
        'custom-member'=>[
            '0'=> [
                'path'=>app_path().DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR.'Member.php',
                'type'=>'controller',
                'scan_mode'=>'file'
             ],
            '1'=> [
                'path'=>app_path().DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR.'Agency.php',
                'type'=>'controller',
                'scan_mode'=>'file'
             ],
            '2'=> [
                'path'=>app_path().DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR.'WebhookPayment.php',
                'type'=>'controller',
                'scan_mode'=>'file'
             ],
             '3'=> [
                'path'=>resource_path('views').DIRECTORY_SEPARATOR.'member',
                'type'=>'view',
                'scan_mode'=>'directory'
             ]
        ],
        'custom-bot'=>[
            '0'=> [
                'path'=>app_path().DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR.'Bot.php',
                'type'=>'controller',
                'scan_mode'=>'file'
            ],            
            '1'=> [
                'path'=>resource_path('views').DIRECTORY_SEPARATOR.'telegram'.DIRECTORY_SEPARATOR.'bot',
                'type'=>'view',
                'scan_mode'=>'directory'
            ],
            '2'=> [
                'path'=>resource_path('views').DIRECTORY_SEPARATOR.'telegram'.DIRECTORY_SEPARATOR.'group',
                'type'=>'view',
                'scan_mode'=>'directory'
            ],
            '3'=> [
                'path'=>app_path().DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR.'Subscriber.php',
                'type'=>'controller',
                'scan_mode'=>'file'
            ],
        ],
        'custom-subscription'=>[
            '0'=> [
                'path'=>app_path().DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR.'Subscription.php',
                'type'=>'controller',
                'scan_mode'=>'file'
             ],
             '1'=> [
                'path'=>resource_path('views').DIRECTORY_SEPARATOR.'subscription',
                'type'=>'view',
                'scan_mode'=>'directory'
             ]
        ],
        'custom-landing'=>[            
            '0'=> [
                'path'=>resource_path('views').DIRECTORY_SEPARATOR.'shared'.DIRECTORY_SEPARATOR.'variables_landing.blade.php',
                'type'=>'view',
                'scan_mode'=>'file'
            ],
            '1'=> [
                'path'=>app_path().DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR.'Landing.php',
                'type'=>'controller',
                'scan_mode'=>'file'
            ],
            '2'=> [
                'path'=>resource_path('views').DIRECTORY_SEPARATOR.'landing',
                'type'=>'view',
                'scan_mode'=>'directory'
            ],
            '3'=> [
                'path'=>resource_path('views').DIRECTORY_SEPARATOR.'layouts'.DIRECTORY_SEPARATOR.'landing.blade.php',
                'type'=>'view',
                'scan_mode'=>'file'
            ],
            '4'=> [
                'path'=>resource_path('views').DIRECTORY_SEPARATOR.'layouts'.DIRECTORY_SEPARATOR.'landing-new.blade.php',
                'type'=>'view',
                'scan_mode'=>'file'
            ]
        ],
        'custom-docs'=>[
            '0'=> [
                'path'=>app_path().DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR.'Docs.php',
                'type'=>'controller',
                'scan_mode'=>'file'
             ],
             '1'=> [
                'path'=>resource_path('views').DIRECTORY_SEPARATOR.'layouts'.DIRECTORY_SEPARATOR.'docs.blade.php',
                'type'=>'view',
                'scan_mode'=>'file'
             ],
             '2'=> [
                'path'=>resource_path('views').DIRECTORY_SEPARATOR.'docs',
                'type'=>'view',
                'scan_mode'=>'directory'
             ]
        ]
    ]
];
