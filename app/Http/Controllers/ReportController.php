<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    const SQL_AND = " AND ";
    const SQL_IN = " IN ('";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function reportFilters($field){
        flush();
        foreach ($this->tables as $table){
            foreach ($this->tables[$table[0]][1] as $attr){
                if($field === $attr){
                    $filterdata = $this->textSort($field, $table[0], $attr);
                    return response()->json([ 'filterdata' => $filterdata ]);
                }
            }
        }
    }
    
    public function reportScope(Request $request) {
        flush();
        $buScope = DB::table($this->tables[self::TBL_BU][0])->whereIn($this->tables[self::TBL_BU][1][0], $request->buid)->pluck($this->tables[self::TBL_BU][1][1]);
        return response()->json([ 'buscope' => $buScope ]);
    }
    
    public function utilizationSummary()
    {
        flush();
        return view('objects.utilizationsummary');
    }
    
    public function utilizationSummaryData(Request $request) {
        flush();
        $query = "SELECT buname, hodname, year, month, ptype, pcode, rname, sah, sae, pae, sbe, pbe FROM (SELECT (CASE bu WHEN @curBU THEN @curBURow:=@curBURow+1 ELSE @curBURow:=1 END) AS seqbu, @curBU:=bu AS budata, (CASE WHEN @curBURow=1 THEN bu ELSE '' END) AS buname, (CASE bu WHEN @curBU THEN CASE hod WHEN @curHOD THEN @curHODRow:=@curHODRow+1 ELSE @curHODRow:=1 END END) AS seqhodname, @curHOD:=hod AS hodnamedata, (CASE WHEN @curHODRow=1 THEN hod ELSE '' END) AS hodname, (CASE bu WHEN @curBU THEN CASE hod WHEN @curHOD THEN CASE yr WHEN @curYear THEN @curYearRow:=@curYearRow+1 ELSE @curYearRow:=1 END END END) AS seqyear, @curYear:=yr AS yeardata, (CASE WHEN @curYearRow=1 THEN yr ELSE '' END) AS year, (CASE bu WHEN @curBU THEN CASE hod WHEN @curHOD THEN CASE yr WHEN @curYear THEN CASE mnt WHEN @curMonth THEN @curMonthRow:=@curMonthRow+1 ELSE @curMonthRow:=1 END END END END) AS seqmonth, @curMonth:=mnt AS monthdata, (CASE WHEN @curMonthRow=1 THEN mnt ELSE '' END) AS month, (CASE bu WHEN @curBU THEN CASE hod WHEN @curHOD THEN CASE yr WHEN @curYear THEN CASE mnt WHEN @curMonth THEN CASE pt WHEN @curPtype THEN @curPtypeRow:=@curPtypeRow+1 ELSE @curPtypeRow:=1 END END END END END) AS seqptype, @curPtype:=pt AS ptypedata, (CASE WHEN @curPtypeRow=1 THEN pt ELSE '' END) AS ptype, (CASE bu WHEN @curBU THEN CASE hod WHEN @curHOD THEN CASE yr WHEN @curYear THEN CASE mnt WHEN @curMonth THEN CASE pt WHEN @curPtype THEN CASE pc WHEN @curPcode THEN @curPcodeRow:=@curPcodeRow+1 ELSE @curPcodeRow:=1 END END END END END END) AS seqpcode, @curPcode:=pc AS pcodedata, (CASE WHEN @curPcodeRow=1 THEN pc ELSE '' END) AS pcode, rname, sah, sae, pae, sbe, pbe FROM (SELECT tblbu.buname AS bu, tblhod.hodname AS hod, tblperiod.year AS yr, tblperiod.month AS mnt, tblprotype.ptype AS pt, tblproject.pcode AS pc, tblresource.rname AS rname, FORMAT(SUM(ah), 2) AS sah, FORMAT(SUM(ae), 2) AS sae, FORMAT((SUM(ae)/SUM(ah))*100,2) AS pae, FORMAT(SUM(be), 2) AS sbe, FORMAT((SUM(be)/SUM(ah))*100,2) AS pbe FROM `tblallocation` JOIN tblbu ON tblallocation.buid = tblbu.buid JOIN tblhod ON tblallocation.hodid = tblhod.hodid JOIN tblperiod ON tblallocation.periodid = tblperiod.periodid JOIN tblprotype ON tblallocation.ptid = tblprotype.ptid JOIN tblproject ON tblallocation.pid = tblproject.pid JOIN tblresource ON tblallocation.rid = tblresource.rid";
        $whereArr = [];
        foreach ($request->all() as $key => $input){
            if($input != null){
                array_push($whereArr, $key.self::SQL_IN.implode("','", $input)."')");
            }
        }
        if($whereArr != null){
            $query = $query." WHERE ".implode(self::SQL_AND, $whereArr);
        }
        $query = $query." GROUP BY bu, hod, yr, mnt, pt, pc, rname WITH ROLLUP) AS initial JOIN(SELECT @curBURow:=0,@curBU:=0,@curHODRow:=0,@curHOD:=0,@curYearRow:=0,@curYear:=0,@curMonthRow:=0,@curMonth:=0,@curPtypeRow:=0,@curPtype:=0,@curPcodeRow:=0,@curPcode:=0) v WHERE bu IS NOT NULL OR ((hod IS NOT NULL OR yr IS NOT NULL OR mnt IS NOT NULL OR pt IS NOT NULL OR pc IS NOT NULL AND rname IS NOT NULL) OR (hod IS NULL OR yr IS NULL OR mnt IS NULL AND pt IS NULL AND pc IS NULL AND rname IS NULL)) ORDER BY bu, hod, yr, field(mnt,'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'), pt, pc, rname) AS final";
        $result = mysqli_query($this->con, $query);
        $reportdata = mysqli_fetch_all($result);
        return response()->json([ 'reportdata' => $reportdata ]);
    }
    
    public function associateUtilizationSummary()
    {
        flush();
        return view('objects.associateutilizationsummary');
    }
    
    public function associateUtilizationSummaryData(Request $request) {
        flush();
        $query = "SELECT rname, year, month, ptype, pcode, buname, hodname, sah, sae, pae, sbe, pbe FROM (SELECT (CASE rn WHEN @curRes THEN @curResRow:=@curResRow+1 ELSE @curResRow:=1 END) AS seqrname, @curRes:=rn AS rnamedata, (CASE WHEN @curResRow=1 THEN rn ELSE '' END) AS rname, (CASE rn WHEN @curRes THEN CASE yr WHEN @curYear THEN @curYearRow:=@curYearRow+1 ELSE @curYearRow:=1 END END) AS seqyear, @curYear:=yr AS yeardata, (CASE WHEN @curYearRow=1 THEN yr ELSE '' END) AS year, (CASE rn WHEN @curRes THEN CASE yr WHEN @curYear THEN CASE mnt WHEN @curMonth THEN @curMonthRow:=@curMonthRow+1 ELSE @curMonthRow:=1 END END END) AS seqmonth, @curMonth:=mnt AS monthdata, (CASE WHEN @curMonthRow=1 THEN mnt ELSE '' END) AS month, (CASE rn WHEN @curRes THEN CASE yr WHEN @curYear THEN CASE mnt WHEN @curMonth THEN CASE pt WHEN @curPtype THEN @curPtypeRow:=@curPtypeRow+1 ELSE @curPtypeRow:=1 END END END END) AS seqptype, @curPtype:=pt AS ptypedata, (CASE WHEN @curPtypeRow=1 THEN pt ELSE '' END) AS ptype, (CASE rn WHEN @curRes THEN CASE yr WHEN @curYear THEN CASE mnt WHEN @curMonth THEN CASE pt WHEN @curPtype THEN CASE pc WHEN @curPcode THEN @curPcodeRow:=@curPcodeRow+1 ELSE @curPcodeRow:=1 END END END END END) AS seqpcode, @curPcode:=pc AS pcodedata, (CASE WHEN @curPcodeRow=1 THEN pc ELSE '' END) AS pcode, (CASE rn WHEN @curRes THEN CASE yr WHEN @curYear THEN CASE mnt WHEN @curMonth THEN CASE pt WHEN @curPtype THEN CASE pc WHEN @curPcode THEN CASE bu WHEN @curBU THEN @curBURow:=@curBURow+1 ELSE @curBURow:=1 END END END END END END) AS seqbuname, @curBU:=bu AS bunamedata, (CASE WHEN @curBURow=1 THEN bu ELSE '' END) AS buname, hodname, sah, sae, pae, sbe, pbe FROM (SELECT tblresource.rname AS rn, tblperiod.year AS yr, tblperiod.month AS mnt, tblprotype.ptype AS pt, tblproject.pcode AS pc, tblbu.buname AS bu, tblhod.hodname AS hodname, FORMAT(SUM(ah), 2) AS sah, FORMAT(SUM(ae), 2) AS sae, FORMAT((SUM(ae)/SUM(ah))*100,2) AS pae, FORMAT(SUM(be), 2) AS sbe, FORMAT((SUM(be)/SUM(ah))*100,2) AS pbe FROM `tblallocation` JOIN tblresource ON tblallocation.rid = tblresource.rid JOIN tblperiod ON tblallocation.periodid = tblperiod.periodid JOIN tblprotype ON tblallocation.ptid = tblprotype.ptid JOIN tblproject ON tblallocation.pid = tblproject.pid JOIN tblhod ON tblallocation.hodid = tblhod.hodid JOIN tblbu ON tblallocation.buid = tblbu.buid";
        $whereArr = [];
        foreach ($request->all() as $key => $input){
            if($input != null){
                array_push($whereArr, $key.self::SQL_IN.implode("','", $input)."')");
            }
        }
        if($whereArr != null){
            $query = $query." WHERE ".implode(self::SQL_AND, $whereArr);
        }
        $query = $query." GROUP BY rn, yr, mnt, pt, pc, bu, hodname WITH ROLLUP) AS initial JOIN(SELECT @curResRow:=0,@curRes:=0,@curYearRow:=0,@curYear:=0,@curMonthRow:=0,@curMonth:=0,@curPtypeRow:=0,@curPtype:=0,@curPcodeRow:=0,@curPcode:=0,@curBURow:=0,@curBU:=0) v WHERE rn IS NOT NULL OR ((yr IS NOT NULL OR mnt IS NOT NULL OR pt IS NOT NULL OR pc IS NOT NULL OR bu IS NOT NULL AND hodname IS NOT NULL) OR (yr IS NULL OR mnt IS NULL AND pt IS NULL AND pc IS NULL AND rn IS NULL)) ORDER BY rn, yr, field(mnt,'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'), pt, pc, bu, hodname) AS final";
        $result = mysqli_query($this->con, $query);
        $reportdata = mysqli_fetch_all($result);
        return response()->json([ 'reportdata' => $reportdata ]);
    }
    
    //write to receive array of text sorts
    public function textSort($field, $table, $attr) {
        if($field === $attr && $field === 'month'){
            return DB::table($table)->distinct()->orderByRaw("field($attr, 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December')")->pluck($attr);
        }else if($field === $attr){
            return DB::table($table)->distinct()->orderBy($attr)->pluck($attr)->toArray();
        }
    }
    
    public function getMax(Request $request) {
        $query = DB::table($this->tables[self::TBL_ALLOCATION][0])
            ->select($this->tables[self::TBL_BU][0].'.'.$this->tables[self::TBL_BU][1][1].' AS buname', $this->tables[self::TBL_HOD][0].'.'.$this->tables[self::TBL_HOD][1][1].' AS hodname', $this->tables[self::TBL_PERIOD][0].'.'.$this->tables[self::TBL_PERIOD][1][2].' AS year', $this->tables[self::TBL_PERIOD][0].'.'.$this->tables[self::TBL_PERIOD][1][1].' AS month', $this->tables[self::TBL_PRO_TYPE][0].'.'.$this->tables[self::TBL_PRO_TYPE][1][1].' AS ptype', $this->tables[self::TBL_PROJECT][0].'.'.$this->tables[self::TBL_PROJECT][1][1].' AS pcode', $this->tables[self::TBL_RESOURCE][0].'.'.$this->tables[self::TBL_RESOURCE][1][2].' AS rname', DB::raw('SUM(ah) AS sah, SUM(ae) AS sae, (SUM(ae)/(SUM(ah)))*100 AS pae, SUM(be) AS sbe, (SUM(be)/(SUM(ah)))*100 AS pbe'))
            ->join($this->tables[self::TBL_BU][0], $this->tables[self::TBL_ALLOCATION][0].'.'.$this->tables[self::TBL_ALLOCATION][1][1],'=', $this->tables[self::TBL_BU][0].'.'.$this->tables[self::TBL_BU][1][0])
            ->join($this->tables[self::TBL_HOD][0], $this->tables[self::TBL_ALLOCATION][0].'.'.$this->tables[self::TBL_ALLOCATION][1][2],'=', $this->tables[self::TBL_HOD][0].'.'.$this->tables[self::TBL_HOD][1][0])
            ->join($this->tables[self::TBL_PROJECT][0], $this->tables[self::TBL_ALLOCATION][0].'.'.$this->tables[self::TBL_ALLOCATION][1][3],'=', $this->tables[self::TBL_PROJECT][0].'.'.$this->tables[self::TBL_PROJECT][1][0])
            ->join($this->tables[self::TBL_PRO_TYPE][0], $this->tables[self::TBL_ALLOCATION][0].'.'.$this->tables[self::TBL_ALLOCATION][1][4],'=', $this->tables[self::TBL_PRO_TYPE][0].'.'.$this->tables[self::TBL_PRO_TYPE][1][0])
            ->join($this->tables[self::TBL_RESOURCE][0], $this->tables[self::TBL_ALLOCATION][0].'.'.$this->tables[self::TBL_ALLOCATION][1][5],'=', $this->tables[self::TBL_RESOURCE][0].'.'.$this->tables[self::TBL_RESOURCE][1][0])
            ->join($this->tables[self::TBL_PERIOD][0], $this->tables[self::TBL_ALLOCATION][0].'.'.$this->tables[self::TBL_ALLOCATION][1][7],'=', $this->tables[self::TBL_PERIOD][0].'.'.$this->tables[self::TBL_PERIOD][1][0]);
        
        $whereArr = [];
        foreach ($request->all() as $key => $input){
            if($input != null){
                array_push($whereArr, $key.self::SQL_IN.implode("','", $input)."')");
            }
        }
        if($whereArr != null){
            $query->whereRaw(implode(self::SQL_AND, $whereArr));
        }
        $query->groupBy(DB::raw('buname, hodname, year, month, ptype, pcode, rname WITH ROLLUP'));
        
        $max = DB::table(DB::raw('('. $query->toSql() .') AS final '))->selectRaw('FLOOR(MAX(pae)) AS maxpae, FLOOR(MAX(pbe)) AS maxpbe')->whereNotNull($this->tables['tblresource'][1][2])->first();
        return response()->json($max);
    }
    
    public function getLatestPeriod() {
        $year = DB::table('tblperiod')->select('year')->orderBy('year', 'desc')->first();
        if($year){
            $month = DB::table('tblperiod')->select('month')->where('year', $year->year)->orderByRaw("field(month, 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December') DESC")->first();
            return response()->json([ 'latestyear' => $year->year , 'latestmonth' => $month->month ]);
        }
    }
}