<?php
/**
 * Created by PhpStorm.
 * User: dmitrijponizov
 * Date: 28.05.2018
 * Time: 11:36
 */

namespace App\Traits;


trait Orderable
{
    public function scopeLatestFirst($query )
    {
        return $query->orderBy('created_at','desc');
    }
    public function scopeOldestFirst($query )
    {
        return $query->orderBy('created_at','asc');
    }

}