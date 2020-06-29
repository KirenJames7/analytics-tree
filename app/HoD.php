<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HoD extends Model
{
    protected $table = 'tblhod';
    protected $primaryKey = 'hodid';
    public $timestamps = false;
    protected $fillable = ['hodname'];
}
