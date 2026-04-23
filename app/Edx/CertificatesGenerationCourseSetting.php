<?php

namespace App\Edx;

use App\Edx\GeneratedCertificate;
use App\Edx\StudentCourseware;
use Illuminate\Database\Eloquent\Model;
use DB;
use Cookie;

class CertificatesGenerationCourseSetting extends Model
{
    //set connection for model
    protected $connection = 'edx_mysql';

    //Set table for model
    protected $table = 'certificates_certificategenerationcoursesetting';

    //Disable timestamps
    public $timestamps = false;


    protected $fillable = [
        'course_key',
        'language_specific_templates_enabled',
        'self_generation_enabled',
        'include_hours_of_effort',
        'created',
        'modified',
    ];
}
