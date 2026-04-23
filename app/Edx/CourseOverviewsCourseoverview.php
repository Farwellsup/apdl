<?php

namespace App\Edx;

use Illuminate\Database\Eloquent\Model;

class CourseOverviewsCourseoverview extends Model
{
    protected $table = "course_overviews_courseoverview";

    protected $connection = "edx_mysql";

      //Disable timestamps
      public $timestamps = false;

    protected $primaryKey = "id";

    protected $keyType = "string";

    protected $fillable = [
        'created',
        'modified',
        'version' ,
        'id',
        '_location' ,
        'display_name' ,
        'display_number_with_default' ,
        'display_org_with_default'  ,
        'start'  ,
        'end'  ,
        'advertised_start'  ,
        'course_image_url'  ,
        'social_sharing_url'  ,
        'end_of_course_survey_url'  ,
        'certificates_display_behavior'  ,
        'certificates_show_before_end'  ,
        'cert_html_view_enabled'  ,
        'has_any_active_web_certificate'  ,
        'cert_name_short'  ,
        'cert_name_long'  ,
        'lowest_passing_grade'  ,
        'days_early_for_beta'  ,
        'mobile_available'  ,
        'visible_to_staff_only'  ,
        '_pre_requisite_courses_json'  ,
        'enrollment_start'  ,
        'enrollment_end'  ,
        'enrollment_domain'  ,
        'invitation_only'  ,
        'max_student_enrollments_allowed'  ,
        'announcement'  ,
        'catalog_visibility'  ,
        'course_video_url'  ,
        'effort'  ,
        'short_description'  ,
        'org'  ,
        'self_paced'  ,
        'marketing_url'  ,
        'eligible_for_financial_aid'  ,
        'language' ,
    ];

    public function getCourseOverviewsCourseoverviewimageset()
    {
        //return $this->hasOne(\common\models\CourseOverviewsCourseoverviewimageset::className(), ['course_overview_id' => 'id']);
    }

    public function getCourseOverviewsCourseoverviewtabs()
    {
        //return $this->hasMany(\common\models\CourseOverviewsCourseoverviewtab::className(), ['course_overview_id' => 'id']);
    }

    public function CourseProfile()
    {
        //return $this->hasOne(\common\models\CourseProfile::className(), ['course_id' => 'id']);
        return $this->hasOne('App\CourseProfile','course_id','id');
    }

    public function CourseAuthAssignments()
    {
        return $this->hasMany('App\CourseAuthAssignment', 'course_id', 'id');
    }

    public function CourseCompletion()
    {
        //return $this->hasMany('App\Edxapp\CertificateCompleted', 'course_id', 'id');["course_id","user_id"],["course_id","user_id"]
        return $this->hasMany('App\CourseCompletion', 'course_id', 'id')
            ->where("percent",">",0)->where("passed","=",1);
    }

    public function CourseEnrolled()
    {
        //return $this->hasMany(\common\models\CourseAuthAssignment::className(), ['course_id' => 'id']);
        return $this->hasMany('App\Edxapp\StudentCourseenrollment', 'course_id', 'id');
    }

    public function getAssignments()
    {
        $assignments = [];
        $roles = CourseAuthAssignment::where(['course_id'=>$this->id])->all();
        if (!$roles) {
            return null;
        }
        foreach ($roles as $role){
            $assignments[]=$role->item_name;
        }
        //return $assignmentsarray;
        return json_encode($assignments);
    }

    public function getSmeCategories()
    {
        $assignments = [];
        $roles = CourseAuthAssignment::find()->where(['course_id'=>$this->id])->all();
        if (!$roles) {
            return [];
        }
        foreach ($roles as $role){
            if ($role->item_name !== 'admin') {
                $assignments[]=$role->item_name;
            }
        }
        return $assignments;

    }
}
