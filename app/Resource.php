<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $table = 'tblresource';
    protected $primaryKey = 'rid';
    public $timestamps = false;
    protected $fillable = ['epf', 'rname'];
}
