<?php

namespace App\Edx;

use Illuminate\Database\Eloquent\Model;


class AccessToken extends Model
{

    //set connection for model
    protected $connection = 'edx_mysql';

    //Set table for model
    protected $table = 'oauth2_provider_accesstoken';

    //Disable timestamps
    public $timestamps = false;
}
