
<?php

return [
    'app' => [
        'APP_DOMAIN' => 'apd.farwell-consultants.com',
        'VERIFY_SSL' => true,
        'PASSMARK' => 0.6,
    ],

    'lms' => [
        'status' => 'test',
        'live' => [
            'EDX_KEY' => 'login-service-client-id',
            'EDX_SECRET' => '1z6y6HGNcMsHw5lMcyF191dJzlzHdjwMMGMTyFxmrzvpD5T8oAJzhVuiDSRjwX7d42Ziz7cPleTR16XhCvWj1Dq2Ez0TK7UK0I3ajd3JxwFyqWiYiEmXMDpmLd5A57Ha',
            'EDX_CONNECT' => true,
            'COOKIE_DOMAIN' => 'lms.farwell-consultants.com',
            'LMS_DOMAIN' => 'https://lms.farwell-consultants.com',
            'LMS_BASE' => 'https://lms.farwell-consultants.com',
            'CMS_BASE' => 'https://studio.lms.farwell-consultants.com',
            'LMS_REGISTRATION_URL' => 'https://lms.farwell-consultants.com/user_api/v1/account/registration/',
            'LMS_LOGIN_URL' => 'https://lms.farwell-consultants.com/user_api/v1/account/login_session/',
            'LMS_RESET_PASSWORD_PAGE' => 'https://lms.farwell-consultants.com/user_api/v1/account/password_reset/',
            'LMS_RESET_PASSWORD_API_URL' => 'https://lms.farwell-consultants.com/user_api/v1/account/password_reset/',
            'edxWebServiceApiToken' => 'c90685ce8bc75a8e03ad35deb28a5fade4a4cc87',
            'default_token' => '133ad7f0ecd269b63637a522dbb529c50475a493'
        ],

    ],

    'zoom' =>[
        'ZOOM_API_URL'=>"https://api.zoom.us/v2/",
        'ZOOM_API_KEY'=>"gZ3yJ6bhRauRrmZTSyk9Gg",
        'ZOOM_API_SECRET'=>"41Z9cZusqrZbeG7UUwgfIZ60UgXaBTgY",
        'ZOOM_ACCOUNT_ID'=> "Ju4g39PgRSmvyF_wvYsLbg"

     ],
     
  
];
