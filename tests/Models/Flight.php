<?php

namespace Shomisha\UnusualRelationships\Test\Models;

use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    public function members()
    {
        return $this->belongsToMany(Member::class);
    }
}
