<?php

namespace App\Models;

use A17\Twill\Models\Behaviors\HasPosition;
use A17\Twill\Models\Behaviors\Sortable;
use Illuminate\Database\Eloquent\Model;
use A17\Twill\Models\Role as TwillRole;

class Role extends TwillRole implements Sortable
{
    use HasPosition;

    //
}
