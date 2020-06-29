<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Allocation extends Model
{
    protected $table = 'tblallocation';
    protected $primaryKey = 'drid';
    public $timestamps = false;
    protected $fillable = ['buid', 'hodid', 'pid', 'ptid', 'rid', 'rtid', 'periodid', 'ah', 'ae', 'be', 'uploadid'];
}
