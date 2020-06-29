<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Test;
use App\Allocation;
use App\BusinessUnit;
use App\HoD;
use App\Period;
use App\Project;
use App\ProjectType;
use App\Resource;
use App\ResourceType;

class FileUploadController extends Controller
{
    protected $currentbusObj;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index() {
        return view('objects.import');
    }
    
    public function currentUploadList()
    {
        $currentUploadList = DB::table($this->tables[self::TBL_PERIOD][0])->select($this->tables[self::TBL_PERIOD][1][1], $this->tables[self::TBL_PERIOD][1][2])->get();
        return response()->json([ 'currentuploadlist' => $currentUploadList ]);
    }
    
    public function uploadFile(Request $request){
        $this->currentbusObj = DB::table($this->tables[self::TBL_BU][0])->distinct()->pluck($this->tables[self::TBL_BU][1][1]);
        $this->doExcel($request);
        $newbus = $this->newBUs($request);
        $uploadData = $this->getRecentUpload($this->tables[self::TBL_ALLOCATION][0], $this->tables[self::TBL_ALLOCATION][1], $this->tables[self::TBL_PERIOD][0], $this->tables[self::TBL_PERIOD][1], $request->month, $request->year);
        return response()->json([ 'success' => 'Data set of '. $request->month .' '. $request->year .' has been successfully imported and ready for reports.', 'upload' => $uploadData, 'month' => $request->month, 'year' => $request->year, 'newbus' => $newbus ]);
    }
    
    public function xgetProjectHOD($spreadsheet, $highestRow) {
        $projectHODxl = $spreadsheet->getActiveSheet()->rangeToArray(
            'A4:A' . $highestRow
        );

        $projectHODData = $this->makeArrayUnique($projectHODxl);
        
        $table = new HoD;
        
        $this->insertToTable($table, $this->tables[self::TBL_HOD][1], $projectHODData);
    }
    
    public function xgetProjectCode($spreadsheet, $highestRow) {
        $projectCodexl = $spreadsheet->getActiveSheet()->rangeToArray(
            'B4:B' . $highestRow
        );

        $projectCodeData = $this->makeArrayUnique($projectCodexl);
        
        $table = new Project;
        
        $this->insertToTable($table, $this->tables[self::TBL_PROJECT][1], $projectCodeData);
    }
    
    public function xgetProjectType($spreadsheet, $highestRow) {
        $projectTypexl = $spreadsheet->getActiveSheet()->rangeToArray(
            'C4:C' . $highestRow
        );

        $projectTypeData = $this->makeArrayUnique($projectTypexl);
        
        $table = new ProjectType;
        
        $this->insertToTable($table, $this->tables[self::TBL_PRO_TYPE][1], $projectTypeData);
    }
    
    public function xgetResource($spreadsheet, $highestRow) {
        $resourceEPFTemp = $spreadsheet->getActiveSheet()->rangeToArray(
            'F4:F' . $highestRow
        );

        $resourceNameTemp = $spreadsheet->getActiveSheet()->rangeToArray(
            'D4:D' . $highestRow
        );

        $resourcexl = $resourceEPFTemp;
        foreach ($resourceNameTemp as $i => $val){
            array_push($resourcexl[$i], $resourceNameTemp[$i][0]);
        }
        
        $resourceData = $this->makeArrayUnique($resourcexl);
        
        $table = new Resource;
        
        $this->insertToTable($table, $this->tables[self::TBL_RESOURCE][1], $resourceData);
    }
    
    public function xgetResourceType($spreadsheet, $highestRow) {
        $resourceTypexl = $spreadsheet->getActiveSheet()->rangeToArray(
            'E4:E' . $highestRow
        );

        $resourceTypeData = $this->makeArrayUnique($resourceTypexl);
        
        $table = new ResourceType;
        
        $this->insertToTable($table, $this->tables[self::TBL_RES_TYPE][1], $resourceTypeData);
    }
    
    public function xgetBU($spreadsheet, $highestRow) {
        $buxl = $spreadsheet->getActiveSheet()->rangeToArray(
            'G4:G' . $highestRow
        );

        $buData = $this->makeArrayUnique($buxl);
        
        $table = new BusinessUnit;
        
        $this->insertToTable($table, $this->tables[self::TBL_BU][1], $buData);
    }
    
    public function xgetDataSet($spreadsheet, $highestRow, $uploadFile, $periodID) {
        $dataSet = $spreadsheet->getActiveSheet()->rangeToArray(
            'A4:J' . $highestRow
        );
        $newDataSet = [];
        foreach ($dataSet as $dataRecord) {
            array_push($newDataSet, [
                $buID = $this->getTableID($this->tables[self::TBL_BU][0], $this->tables[self::TBL_BU][1], $dataRecord[6]), $hodID = $this->getTableID($this->tables[self::TBL_HOD][0], $this->tables[self::TBL_HOD][1], $dataRecord[0]), $projectID = $this->getTableID($this->tables[self::TBL_PROJECT][0], $this->tables[self::TBL_PROJECT][1], $dataRecord[1]), $protypeID = $this->getTableID($this->tables[self::TBL_PRO_TYPE][0], $this->tables[self::TBL_PRO_TYPE][1], $dataRecord[2]), $resourceID = $this->getTableID($this->tables[self::TBL_RESOURCE][0], $this->tables[self::TBL_RESOURCE][1], $dataRecord[5]), $restypeID = $this->getTableID($this->tables[self::TBL_RES_TYPE][0], $this->tables[self::TBL_RES_TYPE][1], $dataRecord[4]), $periodID, $dataRecord[7], $dataRecord[8], $dataRecord[9], $uploadFile
            ]);
        }
        
        $table = new Allocation;
        
        $this->insertToTable($table, $this->tables[self::TBL_ALLOCATION][1], $newDataSet);
    }
    
    public function doExcel($request) {
        $stored = $request->inputFile->storeAs('uploadfiles', $request->month . ' ' . $request->year . '.'. $request->inputFile->getClientOriginalExtension());
        if($stored){
            $uploadFile = DB::table($this->tables[self::TBL_UPLOAD][0])->insertGetId([$this->tables[self::TBL_UPLOAD][1][1] => $request->month . ' ' . $request->year . '.'. $request->inputFile->getClientOriginalExtension()]);
            $periodID = DB::table($this->tables[self::TBL_PERIOD][0])->insertGetId([ $this->tables[self::TBL_PERIOD][1][1] => $request->month , $this->tables[self::TBL_PERIOD][1][2] => $request->year ]);
            
            $inputFileType = 'Xlsx';

            $reader = IOFactory::createReader($inputFileType);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load(storage_path().'/app/'.$stored);

            $highestRow = $spreadsheet->getActiveSheet()->getHighestRow();

            $this->xgetProjectHOD($spreadsheet, $highestRow);

            $this->xgetProjectCode($spreadsheet, $highestRow);

            $this->xgetProjectType($spreadsheet, $highestRow);

            $this->xgetResource($spreadsheet, $highestRow);

            $this->xgetResourceType($spreadsheet, $highestRow);

            $this->xgetBU($spreadsheet, $highestRow);

            $this->xgetDataSet($spreadsheet, $highestRow, $uploadFile, $periodID);
        }
    }
    
    public function makeArrayUnique($arrXL) {
        if(is_array($arrXL[0])){
            return $uniqueArrData = array_unique($arrXL, SORT_REGULAR);
        }else{
            $uniqueArr = array_unique($arrXL, SORT_REGULAR);
            foreach ($uniqueArr as $key => $arr) {
                $uniqueArrData[] = $uniqueArr[$key][0];
            }
            return $uniqueArrData;
        }
    }
    
    public function getRecentUpload($table, $columns, $periodTBL, $periodCOL, $month, $year) {
        $getID = array_shift($periodCOL);
        $periodID = DB::table($periodTBL)->where([ $periodCOL[0] => $month, $periodCOL[1] => $year ])->value($getID);
        return $uploadData = DB::table($table)
            ->join($this->tables[self::TBL_BU][0], $table.'.'.$columns[1] ,'=',$this->tables[self::TBL_BU][0].'.'.$this->tables[self::TBL_BU][1][0])
            ->join($this->tables[self::TBL_HOD][0], $table.'.'.$columns[2] ,'=',$this->tables[self::TBL_HOD][0].'.'.$this->tables[self::TBL_HOD][1][0])
            ->join($this->tables[self::TBL_PROJECT][0], $table.'.'.$columns[3] ,'=',$this->tables[self::TBL_PROJECT][0].'.'.$this->tables[self::TBL_PROJECT][1][0])
            ->join($this->tables[self::TBL_PRO_TYPE][0], $table.'.'.$columns[4] ,'=',$this->tables[self::TBL_PRO_TYPE][0].'.'.$this->tables[self::TBL_PRO_TYPE][1][0])
            ->join($this->tables[self::TBL_RESOURCE][0], $table.'.'.$columns[5] ,'=',$this->tables[self::TBL_RESOURCE][0].'.'.$this->tables[self::TBL_RESOURCE][1][0])
            ->join($this->tables[self::TBL_RES_TYPE][0], $table.'.'.$columns[6] ,'=',$this->tables[self::TBL_RES_TYPE][0].'.'.$this->tables[self::TBL_RES_TYPE][1][0])
            ->join($this->tables[self::TBL_PERIOD][0], $table.'.'.$columns[7] ,'=',$this->tables[self::TBL_PERIOD][0].'.'.$this->tables[self::TBL_PERIOD][1][0])
            ->select($this->tables[self::TBL_HOD][0].'.'.$this->tables[self::TBL_HOD][1][1], $this->tables[self::TBL_PROJECT][0].'.'.$this->tables[self::TBL_PROJECT][1][1], $this->tables[self::TBL_PRO_TYPE][0].'.'.$this->tables[self::TBL_PRO_TYPE][1][1], $this->tables[self::TBL_RESOURCE][0].'.'.$this->tables[self::TBL_RESOURCE][1][1], $this->tables[self::TBL_RESOURCE][0].'.'.$this->tables[self::TBL_RESOURCE][1][2], $this->tables[self::TBL_RES_TYPE][0].'.'.$this->tables[self::TBL_RES_TYPE][1][1], $this->tables[self::TBL_BU][0].'.'.$this->tables[self::TBL_BU][1][1], $table.'.'.$columns[8], $table.'.'.$columns[9], $table.'.'.$columns[10])
            ->where([ $table.'.'.$columns[7] => $periodID ])->get();
    }
    
    public function newBUs($request) {
        $recentbusObj = DB::table($this->tables[self::TBL_BU][0])->distinct()->pluck($this->tables[self::TBL_BU][1][1]);
        $recentbus = json_decode(json_encode($recentbusObj, true));
        $currentbus = json_decode(json_encode($this->currentbusObj, true));
        $newbus = array_diff((array)$recentbus, (array)$currentbus);
        if($newbus){
            $this->modifyRoleScope($newbus, $request);
        }
        return $newbus;
    }
    
    public function modifyRoleScope($newbus, $request) {
        $buID = null;
        $allBUroles = DB::table($this->tables[self::TBL_ALL_BU_ACCESS][0])->where($this->tables[self::TBL_ALL_BU_ACCESS][1][1], 1)->pluck($this->tables[self::TBL_ALL_BU_ACCESS][1][0]);
        foreach ($allBUroles as $allburoleID){
            foreach ($newbus as $newbu){
                $buID = DB::table($this->tables[self::TBL_BU][0])->where($this->tables[self::TBL_BU][1][1], $newbu)->value($this->tables[self::TBL_BU][1][0]);
                DB::table($this->tables[self::TBL_ROLE_RESOURCE][0])->insert([ $this->tables[self::TBL_ROLE_RESOURCE][1][0] => $allburoleID , $this->tables[self::TBL_ROLE_RESOURCE][1][1] => $buID ]);
                if($request->session()->get('allbuaccess') === 1){
                    $request->session()->push('rolescope', $buID);
                }
            }
        }
    }
}
