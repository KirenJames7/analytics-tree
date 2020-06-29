<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectType extends Model
{
    protected $table = 'tblprotype';
    protected $primaryKey = 'ptid';
    public $timestamps = false;
    protected $fillable = ['ptype'];
}
