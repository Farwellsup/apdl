<?php

namespace App\Models;

use A17\Twill\Models\Behaviors\HasSlug;
use A17\Twill\Models\Behaviors\HasRevisions;
use A17\Twill\Models\Behaviors\HasPosition;
use A17\Twill\Models\Behaviors\Sortable;
use A17\Twill\Models\Model;

class Menu extends Model implements Sortable
{
    use HasSlug, HasRevisions, HasPosition;

    protected $fillable = [
        'published',
        'title',
        'key',
        'menu_type_id',
        'position',
    ];
    
    public $slugAttributes = [
        'title',
    ];
    
}
