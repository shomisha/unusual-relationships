<?php

namespace Shomisha\UnusualRelationships\Test\Models;

use Illuminate\Database\Eloquent\Model;
use Shomisha\UnusualRelationships\HasUnusualRelationships;

class Performer extends Model
{
    use HasUnusualRelationships;

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function flights()
    {
        return $this->belongsToManyThrough(Flight::class, Member::class);
    }
}
