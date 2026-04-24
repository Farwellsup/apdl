<?php

namespace App\Edx;

use App\Edx\GeneratedCertificate;
use App\Edx\StudentCourseware;
use Illuminate\Database\Eloquent\Model;
use DB;
use Cookie;

class UserApiPreferenceCopy extends Model
{
  //set connection for model
  protected $connection = 'edx_mysql';

  //Set table for model
  protected $table = 'user_api_userpreference_copy';


  public function course(){
    return $this->belongsTo('App\Courses');
  }

  public $timestamps = false;
}
