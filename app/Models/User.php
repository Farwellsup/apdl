<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Models\User as TwillUser;

class User extends TwillUser implements TwillModelContract
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;


     const GENDER_FEMALE = 1;
    const GENDER_MALE = 2;
    const GENDER_UNDEFINED = 3;

    public function __construct(array $attributes = [])
    {
        $this->table = config('twill.users_table', 'twill_users');

        parent::__construct($attributes);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'payroll_number',
        'first_name',
        'last_name',
        'phone_number',
        'email_verified_at',
        'company_id',
        'company_name',
        'job_role_id',
        'job_role_name',
        'department_id',
        'department_name',
        'unit_id',
        'unit_name',
        'country_id',
        'country_name',
        'gender_id',
        'profile_pic',
        'initial_profile',
        'role_id',
        'published',
        'last_login_at',
        'registered_at',
        'policy_agree',
        'reset_pd'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function hasRole($roles): bool
    {
        return in_array(optional($this->role)->name, (array) $roles);
    }

     public static function getGender()
    {
        $types = array(
            array(
                'id' => self::GENDER_FEMALE,
                'title'=>'Female'
            ),
            array(
                'id' => self::GENDER_MALE,
                'title'=>'Male'
            ),
            array(
                'id' => self::GENDER_UNDEFINED,
                'title'=>'Undefined'
            ),
        );
        return $types;
    }


    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }


     public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function getNameValueAttribute()
    {

        if (!empty($this->name)) {

            return $this->name;
        } elseif (!empty($this->first_name) && !empty($this->last_name)) {

            return $this->first_name . ' ' . $this->last_name;
        } else {
            return $this->username;
        }
    }
}
