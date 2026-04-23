<?php

namespace App\Edx;

use App\Edx\GeneratedCertificate;
use App\Edx\StudentCourseware;
use Illuminate\Database\Eloquent\Model;
use DB;
use Cookie;

class StudentCourseAccessRole extends Model
{
    //set connection for model
    protected $connection = 'edx_mysql';

    //Set table for model
    protected $table = 'student_courseaccessrole';

    //Disable timestamps
    public $timestamps = false;

    const INSTRUCTOR = 'instructor';
    const STAFF = 'staff';

    protected $fillable = [
        'org',
        'course_id',
        'role',
        'user_id',

    ];
}
