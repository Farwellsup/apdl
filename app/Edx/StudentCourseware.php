<?php

namespace App\Edx;

use Illuminate\Database\Eloquent\Model;

class StudentCourseware extends Model
{
  //set connection for model
  protected $connection = 'edx_mysql';

  //Set table for model
  protected $table = 'courseware_studentmodule';

  //Disable timestamps
  public $timestamps = false;

}
