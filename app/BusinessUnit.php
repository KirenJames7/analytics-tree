<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusinessUnit extends Model
{
    protected $table = 'tblbu';
    protected $primaryKey = 'buid';
    public $timestamps = false;
    protected $fillable = ['buname'];
}
