<?php

namespace App\Edx;

use App\Edx\GeneratedCertificate;
use App\Edx\StudentCourseware;
use Illuminate\Database\Eloquent\Model;
use DB;
use Cookie;

class CourseModesCoursemode extends Model
{
    //set connection for model
    protected $connection = 'edx_mysql';

    //Set table for model
    protected $table = 'course_modes_coursemode';

    //Disable timestamps
    public $timestamps = false;

    const AUDIT = 'audit';
    const HONOR = 'honor';

    protected $fillable = [
        'course_id',
        'mode_slug',
        'mode_display_name',
        'currency',
        'min_price',
        'suggested_prices',
        'expiration_datetime_is_explicit',

    ];
}
