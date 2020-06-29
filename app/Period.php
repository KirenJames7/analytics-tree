<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    protected $table = 'tblperiod';
    protected $primaryKey = 'periodid';
    public $timestamps = false;
    protected $fillable = ['month', 'year'];
}
