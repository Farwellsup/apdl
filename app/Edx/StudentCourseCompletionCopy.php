<?php

namespace App\Edx;

use App\Edx\GeneratedCertificate;
// use App\Edx\StudentCourseware;
use Illuminate\Database\Eloquent\Model;
use DB;
use Cookie;

class StudentCourseCompletionCopy extends Model
{
  //set connection for model
  protected $connection = 'edx_mysql';

  //Set table for model
  protected $table = 'courseware_studentmodule_copy';

  public function edx_user(){
    return $this->belongsTo('App\Edx\EdxAuthUser','user_id');
  }

  public function course(){
    return $this->belongsTo('App\Courses');
  }

public $timestamps = false;
}
