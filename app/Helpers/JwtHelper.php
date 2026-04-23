<?php

namespace App\Helpers;
use Firebase\JWT\JWT;

class JwtHelper
{
    public static function generateToken(){
        $payload = [
            'iat' => time(),
            'exp' => time() + 60,
        ];
        // dd(config('app.platform_settings.app_hmac_key'));
        return JWT::encode($payload, config('app.platform_settings.app_hmac_key'), 'HS256');
    }
}