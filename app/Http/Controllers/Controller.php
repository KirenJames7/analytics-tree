<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    protected $tables = [];
    protected $con;
    protected $domainip;
    protected $domain;
    protected $domainport;
    protected $ldapconfig;
    protected $ds;
    const TBL_BU = "tblbu";
    const TBL_ROLE = "tblrole";
    const TBL_ROLE_RESOURCE = "tblroleresource";
    const TBL_USER = "tbluser";
    const TBL_ALL_BU_ACCESS = "tblallbuaccess";
    const TBL_PERIOD = "tblperiod";
    const TBL_ALLOCATION = "tblallocation";
    const TBL_HOD = "tblhod";
    const TBL_PROJECT = "tblproject";
    const TBL_PRO_TYPE = "tblprotype";
    const TBL_RESOURCE = "tblresource";
    const TBL_RES_TYPE = "tblrestype";
    const TBL_UPLOAD = "tblupload";

    public function __construct() {
        
        $dbtables = DB::select('SHOW TABLES');
        foreach ($dbtables as $key => $value) {
            $tables = [];
            array_push($tables, $value->Tables_in_aims);
            $tablecolumns = DB::select('SHOW COLUMNS FROM ' . $value->Tables_in_aims);
            $columns = [];
            foreach ($tablecolumns as $column){
                array_push($columns, $column->Field);
            }
            array_push($tables, $columns);
            $this->tables["$value->Tables_in_aims"] = $tables;
        }
        $this->con = mysqli_connect(env('DB_HOST'), env('DB_USERNAME'), env('DB_PASSWORD'), env('DB_DATABASE'));
    }
    
    protected function insertToTable($table, $columns, $dataArr) {
        
        array_shift($columns);
        $insertArr = [];
        if(is_array($dataArr[0])){
            foreach ($dataArr as $row => $datarow) {
                foreach ($columns as $col => $column) {
                    $insertArr[$column] = $datarow[$col];
                }
                $table::firstOrCreate($insertArr);
            }
        }else{
            foreach ($columns as $col => $column) {
                foreach ($dataArr as $row => $datarow) {
                    array_push($insertArr, [$column => $dataArr[$row]] );
                    $table::firstOrCreate($insertArr[$row]);
                }
            }
        }
    }
    
    protected function getTableID($table, $columns, $datacell) {
        
        $getID = array_shift($columns);
        $tempArr = [];
        $insertArr = [];
        if(is_array($datacell)){
            foreach ($columns as $key => $column){
                $tempArr[$column] = $datacell[$key];
            }
            array_push($insertArr, $tempArr);
            return $id = DB::table($table)->where([$insertArr])->value($getID);
        }else{
            return $id = DB::table($table)->where($columns[0], $datacell)->value($getID);
        }
    }
    
    protected function deleteTableData($tables, $columns, $deletecriteria) {
        
        if(is_array($tables)){
            foreach ($tables as $key => $table){
                DB::table($table)->where($columns[$key], $deletecriteria[$key])->delete();
            }
        }else{
            DB::table($table)->where($columns, $deletecriteria)->delete();
        }
    }
}
