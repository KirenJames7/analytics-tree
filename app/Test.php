<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $table = 'tbltest';
    protected $primaryKey = 'testid';
    public $timestamps = false;
    protected $fillable = ['testdata', 'testadd'];
}
