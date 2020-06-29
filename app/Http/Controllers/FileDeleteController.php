<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class FileDeleteController extends Controller
{
    public function index() {
        return view('objects.delete');
    }
    
    public function getYear() {
        $years = DB::table($this->tables[self::TBL_PERIOD][0])->distinct()->orderBy($this->tables[self::TBL_PERIOD][1][2], 'desc')->pluck($this->tables[self::TBL_PERIOD][1][2]);
        return response()->json([ 'years' => $years ]);
    }
    
    public function getMonthsofYear($year) {
        $months = DB::table($this->tables[self::TBL_PERIOD][0])->distinct()->where($this->tables[self::TBL_PERIOD][1][2], $year)->orderByRaw("field(".$this->tables[self::TBL_PERIOD][1][1].", 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December')")->pluck($this->tables[self::TBL_PERIOD][1][1]);
        return response()->json([ 'months' => $months ]);
    }
    
    public function deleteData(Request $request) {
        $periodID = DB::table($this->tables[self::TBL_PERIOD][0])->where([ 'month' => $request->month, 'year' => $request->year ])->value($this->tables[self::TBL_PERIOD][1][0]);
        DB::table(self::TBL_ALLOCATION)->where($this->tables[self::TBL_PERIOD][1][0], $periodID)->delete();
        DB::table($this->tables[self::TBL_PERIOD][0])->where($this->tables[self::TBL_PERIOD][1][0], $periodID)->delete();
        Storage::delete('/uploadfiles/'.$request->month.' '.$request->year.'.xlsx');
        return response()->json([ 'success' => 'Data set of '. $request->month .' '. $request->year .' has been successfully deleted']);
    }
}
