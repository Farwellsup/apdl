<?php

namespace App\Models;

use A17\Twill\Models\Behaviors\HasSlug;
use A17\Twill\Models\Behaviors\HasRevisions;
use A17\Twill\Models\Behaviors\HasPosition;
use A17\Twill\Models\Behaviors\Sortable;
use A17\Twill\Models\Model;

class JobRole extends Model implements Sortable
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
}
