<?php

namespace App\Models;

use A17\Twill\Models\Behaviors\HasSlug;
use A17\Twill\Models\Behaviors\HasRevisions;
use A17\Twill\Models\Behaviors\HasPosition;
use A17\Twill\Models\Behaviors\Sortable;
use A17\Twill\Models\Model;

class Unit extends Model implements Sortable
{
    use HasSlug, HasRevisions, HasPosition;

    protected $fillable = [
        'published',
        'title',
        'company_id',
        'department_id',
        'position',
    ];

    public $slugAttributes = [
        'title',
    ];

    protected $casts = [
        'department_id' => 'array',
    ];


    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getParentValueAttribute()
    {
        return $this->company ? $this->company->title : null;
    }


    public function getDepartmentValueAttribute()
    {

        if ($this->department_id) {

            $dp = Department::whereIn('id', $this->department_id)->pluck('title')->toArray();

            return implode(',', $dp);
        } else {
            return null;
        }
    }
}
