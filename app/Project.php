<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'tblproject';
    protected $primaryKey = 'pid';
    public $timestamps = false;
    protected $fillable = ['pcode'];
}
