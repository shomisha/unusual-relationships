<?php

namespace Shomisha\UnusualRelationships\Test\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    public function flights()
    {
        return $this->belongsToMany(Flight::class);
    }

    public function performer()
    {
        return $this->belongsTo(Performer::class);
    }
}
