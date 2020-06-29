<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResourceType extends Model
{
    protected $table = 'tblrestype';
    protected $primaryKey = 'rtid';
    public $timestamps = false;
    protected $fillable = ['rtype'];
}
