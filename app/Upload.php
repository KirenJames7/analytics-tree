<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    protected $table = 'tblupload';
    protected $primaryKey = 'uploadid';
    public $timestamps = false;
    protected $fillable = ['file'];
}
