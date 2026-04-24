<?php

namespace App\Edx;

use Illuminate\Database\Eloquent\Model;


class AuthRegistrationCopy extends Model
{

  //set connection for model
  protected $connection = 'edx_mysql';

  //Set table for model
  protected $table = 'auth_registration_copy';

    //Disable timestamps
    public $timestamps = false;

  /**
  * @inheritdoc
  */
  public function attributeLabels()
  {
    return [
      'id' => 'ID',
      'activation_key' => 'Activation Key',
      'user_id' => 'User ID',
    ];
  }

  /**
   * @return Model
   */
  public function getUser()
  {
      return $this->hasOne('App\Edx\EdxAuthUser', 'id','user_id');
  }

}
