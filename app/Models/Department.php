<?php

namespace App\Models;

use A17\Twill\Models\Behaviors\HasSlug;
use A17\Twill\Models\Behaviors\HasRevisions;
use A17\Twill\Models\Behaviors\HasPosition;
use A17\Twill\Models\Behaviors\Sortable;
use A17\Twill\Models\Model;

class Department extends Model implements Sortable
{
    use HasSlug, HasRevisions, HasPosition;

    protected $fillable = [
        'published',
        'title',
        'company_id',
        'position',
    ];
    
    public $slugAttributes = [
        'title',
    ];

    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function getParentValueAttribute(){

       return $this->company ? $this->company->title : null;
    }
    
}
