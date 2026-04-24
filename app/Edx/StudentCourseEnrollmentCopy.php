<?php

namespace App\Edx;

use App\Edx\GeneratedCertificate;
use App\Edx\StudentCourseware;
use Illuminate\Database\Eloquent\Model;
use DB;
use Cookie;

class StudentCourseEnrollmentCopy extends Model
{
  //set connection for model
  protected $connection = 'edx_mysql';

  //Set table for model
  protected $table = 'student_courseenrollment_copy';

 protected $fillable = ['course_id', 'created', 'is_active', 'mode', 'user_id'];
 public $timestamps = false;

  public function edx_user(){
    return $this->belongsTo('App\Edx\EdxAuthUser','user_id');
  }

  public function course(){
    return $this->belongsTo('App\Courses');
  }

  public function getGenCert(){

    $configLms = config()->get("settings.lms.live");

    $client = new \GuzzleHttp\Client(
      [
        'verify'=>env('VERIFY_SSL',true),
        'headers'=>['Authorization'=>'Bearer '. $_COOKIE['edinstancexid']
      ]
    ]);
      $request =  $client->request('GET', $configLms['LMS_BASE'].'/api/grades/v0/course_grade/'.$this->course_id.'/users/?username='.$this->edx_user->username);

         if ($request) {
             return json_decode($request->getBody()->getContents());
           }
          return false;

  }


    public function getGenCertificate($course, $username, $token, $userid)
    {

        $configLms = config()->get("settings.lms.live");

        $client = new \GuzzleHttp\Client(
            [
                'verify' => env('VERIFY_SSL', true),
                'headers' => [
                    'Authorization' => 'Bearer ' . $token
                ]
            ]
        );

        $check = StudentCourseware::where('course_id', $course)->where('student_id', $userid);

        if($check){

            $request =  $client->request('GET', $configLms['LMS_BASE'] . '/api/grades/v0/course_grade/' . $course . '/users/?username=' . $username);
            if ($request) {
                if ($request->getStatusCode() == 200) {

                    return json_decode($request->getBody()->getContents());
                } else {

                    return false;
                }
            }
            return false;

        }
        return false;
    }

  public function getCompletion($user, $courses){
  $grades= array();
  $found = null;
  $datas = StudentCourseware::where('student_id',$user)->where('module_type','=','problem')->where('grade','!=','')->get();
  if($datas){
    foreach($datas as $data){
      $grades[] = $data->course_id;
    }
  foreach($grades as $g){
    if (in_array($g,$courses)) {
         $found['qty'] = true;
     }
    }
    return $found;
  }
}

  public function getGenCerts($user, $course){
     // var_dump($user);
     $grades=array();
     $max_grades = array();
    $datas = StudentCourseware::where('course_id',$course)->where('student_id',$user)->where('module_type','=','problem')->get();
     if($datas){
       foreach($datas as $data){
          $grades[] = $data->grade;
          $max_grades[] = $data->max_grade;
       }
      }
    return ['grades' => $grades, 'max_grades' => $max_grades];
  }


  public function getGenRep(){
       $genCert = GeneratedCertificate::where('course_id',$this->course_id)->where('user_id',$this->user_id)->first();


       if($genCert){
          $this->g = $genCert->grade;
          $this->grade = ($genCert->grade * 100).'%';
         switch ($genCert->status) {
           case 'downloadable':
              $this->status = "Completed";
             break;
           case 'notpassing':
           default:
             $this->status = "Completed but Failed";
             break;
         }

       }else{
         $this->status = 'In Progress';
         $this->grade = (0).'%';
       }

    return $genCert;
  }

  public function getGenRept(){
    $genRept = GeneratedCertificate::where('user_id',$this->user_id)->where('status','downloadable')->first();
    return $genRept;
  }


}
