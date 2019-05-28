# Laravel Unusual Relationships

[![Latest Stable Version](https://poser.pugx.org/shomisha/unusual-relationships/v/stable)](https://packagist.org/packages/shomisha/unusual-relationships)
[![Build Status](https://travis-ci.org/weareneopix/laravel-model-translation.svg?branch=master)](https://travis-ci.org/shomisha/unusual-relationships.svg?branch=master)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

This package is meant to provide its users with an API to accessing relationships that aren't included in Laravel's base installation. 
It achieves this effect by utilizing a trait which in turn instantiates custom relationship classes which rely on Laravel's relationship scaffolding.

```php
class Employee extends Model {
    public function tasks() 
    {
        return $this->belongsToMany(Task::class);
    }
    
    public function boss() 
    {
        return $this->belongsTo(Boss::class);
    }
}

class Boss extends Model {
    use Shomisha\UnusualRelationships\HasUnusualRelationships;

    public function employees() 
    {
        return $this->hasMany(Employee::class);
    }
    
    public function tasks()
    {
        return $this->belongsToManyThrough(Task::class, Employee::class)
    }
}
```

The example above shows how you can use the `belongsToManyThrough` relationship to connect your model to its distantly related models. 
To learn more about this which methods work with which database structures, as well as how to use those methods, head over to our  [wiki pages](https://github.com/shomisha/unusual-relationships/wiki).