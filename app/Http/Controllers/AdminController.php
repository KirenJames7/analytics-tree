<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    protected $bus;
    const GROUP_CONCAT_DISTINCT = ",GROUP_CONCAT(DISTINCT ";
    const AS_BU_ID = ") AS buid";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('objects.admin');
    }
    
    public function rolemanagement()
    {
        return view('objects.rolemanagement');
    }
    
    public function systemlogs()
    {
        return view('objects.systemlogs');
    }
    
    public function getBUs()
    {
        $this->bus = DB::table($this->tables[self::TBL_BU][0])->distinct()->pluck($this->tables[self::TBL_BU][1][0], $this->tables[self::TBL_BU][1][1]);
        return response()->json([ 'bus' => $this->bus ]);
    }
    
    public function roleNameValidation(Request $request)
    {
        $rolename = DB::table($this->tables[self::TBL_ROLE][0])->where($request->all())->value($this->tables[self::TBL_ROLE][1][1]);
        return response()->json([ 'rolename' => $rolename ]);
    }
    
    public function addRole(Request $request)
    {
        $request->systemadmin = $request->systemadmin || 0;
        $request->filemanager = $request->filemanager || 0;
        $request->allbuaccess = $request->allbuaccess || 0;
        $roleID = DB::table($this->tables[self::TBL_ROLE][0])->insertGetId([ $this->tables[self::TBL_ROLE][1][1] => $request->rolename , $this->tables[self::TBL_ROLE][1][2] => $request->roledescription , $this->tables[self::TBL_ROLE][1][3] => $request->filemanager , $this->tables[self::TBL_ROLE][1][4] => $request->systemadmin ]);
        foreach ($request->buid as $buid){
            DB::table($this->tables[self::TBL_ROLE_RESOURCE][0])->insert([ $this->tables[self::TBL_ROLE_RESOURCE][1][0] => $roleID , $this->tables[self::TBL_ROLE_RESOURCE][1][1] => $buid ]);
        }
        foreach ($request->username as $username){
            DB::table($this->tables[self::TBL_USER][0])->insert([ $this->tables[self::TBL_USER][1][1] => $username , $this->tables[self::TBL_USER][1][2] => $roleID ]);
        }
        DB::table($this->tables[self::TBL_ALL_BU_ACCESS][0])->insert([ $this->tables[self::TBL_ALL_BU_ACCESS][1][0] => $roleID , $this->tables[self::TBL_ALL_BU_ACCESS][1][1] => $request->allbuaccess ]);
        $newRole = DB::table($this->tables[self::TBL_ROLE][0])->join($this->tables[self::TBL_ROLE_RESOURCE][0], $this->tables[self::TBL_ROLE][0].'.'.$this->tables[self::TBL_ROLE][1][0],'=',$this->tables[self::TBL_ROLE_RESOURCE][0].'.'.$this->tables[self::TBL_ROLE_RESOURCE][1][0])->join($this->tables[self::TBL_USER][0], $this->tables[self::TBL_ROLE][0].'.'.$this->tables[self::TBL_ROLE][1][0],'=',$this->tables[self::TBL_USER][0].'.'.$this->tables[self::TBL_USER][1][2])->join($this->tables[self::TBL_BU][0], $this->tables[self::TBL_ROLE_RESOURCE][0].'.'.$this->tables[self::TBL_ROLE_RESOURCE][1][1],'=', $this->tables[self::TBL_BU][0].'.'.$this->tables[self::TBL_BU][1][0])->join($this->tables[self::TBL_ALL_BU_ACCESS][0], $this->tables[self::TBL_ROLE][0].'.'.$this->tables[self::TBL_ROLE][1][0],'=', $this->tables[self::TBL_ALL_BU_ACCESS][0].'.'.$this->tables[self::TBL_ALL_BU_ACCESS][1][0])->select(DB::raw($this->tables[self::TBL_ROLE][0].'.'.$this->tables[self::TBL_ROLE][1][0].','.$this->tables[self::TBL_ROLE][0].'.'.$this->tables[self::TBL_ROLE][1][1].','.$this->tables[self::TBL_ROLE][0].'.'.$this->tables[self::TBL_ROLE][1][2].self::GROUP_CONCAT_DISTINCT.$this->tables[self::TBL_USER][0].'.'.$this->tables[self::TBL_USER][1][1].') AS username'.self::GROUP_CONCAT_DISTINCT.$this->tables[self::TBL_ROLE_RESOURCE][0].'.'.$this->tables[self::TBL_ROLE_RESOURCE][1][1].self::AS_BU_ID.self::GROUP_CONCAT_DISTINCT.$this->tables[self::TBL_BU][0].'.'.$this->tables[self::TBL_BU][1][1].' SEPARATOR " | ") AS buname,'.$this->tables[self::TBL_ROLE][0].'.'.$this->tables[self::TBL_ROLE][1][3].','.$this->tables[self::TBL_ROLE][0].'.'.$this->tables[self::TBL_ROLE][1][4].','.$this->tables[self::TBL_ALL_BU_ACCESS][0].'.'.$this->tables[self::TBL_ALL_BU_ACCESS][1][1]))->where($this->tables[self::TBL_ROLE][0].'.'.$this->tables[self::TBL_ROLE][1][0] , $roleID)->groupBy($this->tables[self::TBL_ROLE][1][0])->first();
        return response()->json([ 'newrole' => $newRole ]);
    }
    
    public function currentRoles()
    {
        $currentRoles = DB::table($this->tables[self::TBL_ROLE][0])->join($this->tables[self::TBL_ROLE_RESOURCE][0], $this->tables[self::TBL_ROLE][0].'.'.$this->tables[self::TBL_ROLE][1][0],'=',$this->tables[self::TBL_ROLE_RESOURCE][0].'.'.$this->tables[self::TBL_ROLE_RESOURCE][1][0])->join($this->tables[self::TBL_USER][0], $this->tables[self::TBL_ROLE][0].'.'.$this->tables[self::TBL_ROLE][1][0],'=',$this->tables[self::TBL_USER][0].'.'.$this->tables[self::TBL_USER][1][2])->join($this->tables[self::TBL_BU][0], $this->tables[self::TBL_ROLE_RESOURCE][0].'.'.$this->tables[self::TBL_ROLE_RESOURCE][1][1],'=', $this->tables[self::TBL_BU][0].'.'.$this->tables[self::TBL_BU][1][0])->join($this->tables[self::TBL_ALL_BU_ACCESS][0], $this->tables[self::TBL_ROLE][0].'.'.$this->tables[self::TBL_ROLE][1][0],'=', $this->tables[self::TBL_ALL_BU_ACCESS][0].'.'.$this->tables[self::TBL_ALL_BU_ACCESS][1][0])->select(DB::raw($this->tables[self::TBL_ROLE][0].'.'.$this->tables[self::TBL_ROLE][1][0].','.$this->tables[self::TBL_ROLE][0].'.'.$this->tables[self::TBL_ROLE][1][1].','.$this->tables[self::TBL_ROLE][0].'.'.$this->tables[self::TBL_ROLE][1][2].self::GROUP_CONCAT_DISTINCT.$this->tables[self::TBL_ROLE_RESOURCE][0].'.'.$this->tables[self::TBL_ROLE_RESOURCE][1][1].self::AS_BU_ID.self::GROUP_CONCAT_DISTINCT.$this->tables[self::TBL_BU][0].'.'.$this->tables[self::TBL_BU][1][1].' SEPARATOR " | ") AS buname'.self::GROUP_CONCAT_DISTINCT.$this->tables[self::TBL_USER][0].'.'.$this->tables[self::TBL_USER][1][1].') AS username,'.$this->tables[self::TBL_ROLE][0].'.'.$this->tables[self::TBL_ROLE][1][3].','.$this->tables[self::TBL_ROLE][0].'.'.$this->tables[self::TBL_ROLE][1][4].','.$this->tables[self::TBL_ALL_BU_ACCESS][0].'.'.$this->tables[self::TBL_ALL_BU_ACCESS][1][1]))->groupBy($this->tables[self::TBL_ROLE][1][0])->get();
        return response()->json([ 'currentroles' => $currentRoles ]);
    }
    
    public function deleteRole(Request $request)
    {
        DB::table($this->tables[self::TBL_ROLE][0])->where($this->tables[self::TBL_ROLE][1][0], $request->roleid)->delete();
        DB::table($this->tables[self::TBL_ROLE_RESOURCE][0])->where($this->tables[self::TBL_ROLE_RESOURCE][1][0], $request->roleid)->delete();
        DB::table($this->tables[self::TBL_USER][0])->where($this->tables[self::TBL_USER][1][2], $request->roleid)->delete();
        DB::table($this->tables[self::TBL_ALL_BU_ACCESS][0])->where($this->tables[self::TBL_ALL_BU_ACCESS][1][0], $request->roleid)->delete();
        return response()->json([ 'success' => 'success' ]);
    }
    
    public function roleAddUser(Request $request)
    {
        foreach ($request->username as $user){
            DB::table($this->tables[self::TBL_USER][0])->insert([ $this->tables[self::TBL_USER][1][1] => $user , $this->tables[self::TBL_USER][1][2] => $request->roleid ]);
        }
    }
    
    public function roleDeleteUser(Request $request)
    {
        DB::table($this->tables[self::TBL_USER][0])->whereIn($this->tables[self::TBL_USER][1][1], $request->username)->delete();
    }
    
    public function roleModifyScope(Request $request)
    {
        $result = DB::table($this->tables[self::TBL_ROLE_RESOURCE][0])->where($this->tables[self::TBL_ROLE_RESOURCE][1][0], $request->roleid)->delete();
        if($result){
            foreach ($request->buid as $buid){
                DB::table($this->tables[self::TBL_ROLE_RESOURCE][0])->insert([ $this->tables[self::TBL_ROLE_RESOURCE][1][0] => $request->roleid , $this->tables[self::TBL_ROLE_RESOURCE][1][1] => $buid ]);
            }
        }
    }
    
    public function roleModifySystemAdmin(Request $request)
    {
        DB::table($this->tables[self::TBL_ROLE][0])->where($this->tables[self::TBL_ROLE][1][0] , $request->roleid)->update([ $this->tables[self::TBL_ROLE][1][4] => $request->systemadmin ]);
    }
    
    public function roleModifyFileManager(Request $request)
    {
        DB::table($this->tables[self::TBL_ROLE][0])->where($this->tables[self::TBL_ROLE][1][0] , $request->roleid)->update([ $this->tables[self::TBL_ROLE][1][3] => $request->filemanager ]);
    }
    
    public function roleModifyAllBUAccess(Request $request)
    {
        DB::table($this->tables[self::TBL_ALL_BU_ACCESS][0])->where($this->tables[self::TBL_ALL_BU_ACCESS][1][0] , $request->roleid)->update([ $this->tables[self::TBL_ALL_BU_ACCESS][1][1] => $request->allbuaccess ]);
    }
    
    public function getModifiedRole(Request $request)
    {
        $modifiedRole = DB::table($this->tables[self::TBL_ROLE][0])->join($this->tables[self::TBL_ROLE_RESOURCE][0], $this->tables[self::TBL_ROLE][0].'.'.$this->tables[self::TBL_ROLE][1][0],'=',$this->tables[self::TBL_ROLE_RESOURCE][0].'.'.$this->tables[self::TBL_ROLE_RESOURCE][1][0])->join($this->tables[self::TBL_USER][0], $this->tables[self::TBL_ROLE][0].'.'.$this->tables[self::TBL_ROLE][1][0],'=',$this->tables[self::TBL_USER][0].'.'.$this->tables[self::TBL_USER][1][2])->join($this->tables[self::TBL_BU][0], $this->tables[self::TBL_ROLE_RESOURCE][0].'.'.$this->tables[self::TBL_ROLE_RESOURCE][1][1],'=', $this->tables[self::TBL_BU][0].'.'.$this->tables[self::TBL_BU][1][0])->join($this->tables[self::TBL_ALL_BU_ACCESS][0], $this->tables[self::TBL_ROLE][0].'.'.$this->tables[self::TBL_ROLE][1][0],'=', $this->tables[self::TBL_ALL_BU_ACCESS][0].'.'.$this->tables[self::TBL_ALL_BU_ACCESS][1][0])->select(DB::raw($this->tables[self::TBL_ROLE][0].'.'.$this->tables[self::TBL_ROLE][1][0].','.$this->tables[self::TBL_ROLE][0].'.'.$this->tables[self::TBL_ROLE][1][1].','.$this->tables[self::TBL_ROLE][0].'.'.$this->tables[self::TBL_ROLE][1][2].self::GROUP_CONCAT_DISTINCT.$this->tables[self::TBL_USER][0].'.'.$this->tables[self::TBL_USER][1][1].') AS username'.self::GROUP_CONCAT_DISTINCT.$this->tables[self::TBL_ROLE_RESOURCE][0].'.'.$this->tables[self::TBL_ROLE_RESOURCE][1][1].self::AS_BU_ID.self::GROUP_CONCAT_DISTINCT.$this->tables[self::TBL_BU][0].'.'.$this->tables[self::TBL_BU][1][1].' SEPARATOR " | ") AS buname,'.$this->tables[self::TBL_ROLE][0].'.'.$this->tables[self::TBL_ROLE][1][3].','.$this->tables[self::TBL_ROLE][0].'.'.$this->tables[self::TBL_ROLE][1][4].','.$this->tables[self::TBL_ALL_BU_ACCESS][0].'.'.$this->tables[self::TBL_ALL_BU_ACCESS][1][1]))->where($this->tables[self::TBL_ROLE][0].'.'.$this->tables[self::TBL_ROLE][1][0] , $request->roleid)->groupBy($this->tables[self::TBL_ROLE][1][0])->first();
        return response()->json([ 'modifiedrole' => $modifiedRole ]);
    }
}