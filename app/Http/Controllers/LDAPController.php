<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LDAPController extends Controller
{
    protected $bind;
    protected $ldapUsers = [];
    protected $existingUsersObj;
    protected $existingUsers = [];
    protected $allUsers = [];
    protected $password;
    const CURRENT_USER = "currentuser";
    const FILE_MANAGER = "filemanager";
    const SYSTEM_ADMIN = "systemadmin";
    const ROLE_SCOPE = "rolescope";
    const BASE_DN = "basedn";
    const SAM_ACCOUNT_NAME = "samaccountname";
    
    public function __construct() {
        parent::__construct();
        $this->domainip = env('DOMAIN_IP');
        $this->domain = env('DOMAIN_NAME');
        $this->domainport = env('DOMAIN_PORT');
        $this->ldapconfig[self::BASE_DN] = env('BASE_DN');
        $this->ds = ldap_connect($this->domainip, $this->domainport);
        ldap_set_option($this->ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($this->ds, LDAP_OPT_REFERRALS, 0);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function userRegistration() {
        $this->bind = @ldap_bind($this->ds, session('sam') . '@' . $this->domain, session('password'))
            or die();
        if ($this->bind) {
            $filter = "(&(objectCategory=person))";
            $justtheseattributes = array( "sAMAccountName");
            $result = ldap_search($this->ds, $this->ldapconfig[self::BASE_DN], $filter, $justtheseattributes);
            $users = ldap_get_entries($this->ds, $result);
            foreach ($users as $user) {
                if($user[self::SAM_ACCOUNT_NAME][0]){
                    array_push($this->ldapUsers, $user[self::SAM_ACCOUNT_NAME][0]);
                }
            }
            $this->existingUsersObj = DB::table($this->tables[self::TBL_USER][0])->distinct()->pluck($this->tables[self::TBL_USER][1][1]);
            $this->existingUsers = json_decode(json_encode($this->existingUsersObj, true));           
            $this->allUsers = array_values(array_diff($this->ldapUsers , $this->existingUsers));
            return response()->json([ 'users' => $this->allUsers ]);
        }
    }

    public function userAuthorization() {
        
    }

    public function userAuthentication(Request $request) {
        $this->password = $request->password;
        $userroleid = $this->userExistanceCheck($request->username);
        if ($userroleid) {
            $this->bind = @ldap_bind($this->ds, $request->username . '@' . $this->domain, $request->password);
            if ($this->bind) {
                $filter = "(&(samAccountName=$request->username))";
                $justtheseattributes = array("sAMAccountName", "displayName", "mail");
                $result = ldap_search($this->ds, $this->ldapconfig[self::BASE_DN], $filter, $justtheseattributes);
                $sessiondata = ldap_get_entries($this->ds, $result);
                $this->userSession($sessiondata, $userroleid, $request->password);
                return response()->json([ 'authentication' => true, 'session' => [ self::CURRENT_USER => session(self::CURRENT_USER), self::ROLE_SCOPE => session(self::ROLE_SCOPE), self::SYSTEM_ADMIN => session(self::SYSTEM_ADMIN), self::FILE_MANAGER => session(self::FILE_MANAGER) ] ]);
            }else{
                return response()->json([ 'invalidcredentials' => true ]);
            }
        } else {
            return response()->json([ 'authentication' => false ]);
        }
    }
    
    public function userExistanceCheck($username) {
        return DB::table($this->tables[self::TBL_USER][0])->where($this->tables[self::TBL_USER][1][1], $username)->value($this->tables[self::TBL_USER][1][2]);
    }
    
    public function userSession($sessiondata, $userroleid, $password) {
        $filemanager = DB::table($this->tables[self::TBL_ROLE][0])->where($this->tables[self::TBL_ROLE][1][0], $userroleid)->value($this->tables[self::TBL_ROLE][1][3]);
        $systemadmin = DB::table($this->tables[self::TBL_ROLE][0])->where($this->tables[self::TBL_ROLE][1][0], $userroleid)->value($this->tables[self::TBL_ROLE][1][4]);
        $allbuaccess = DB::table($this->tables[self::TBL_ALL_BU_ACCESS][0])->where($this->tables[self::TBL_ALL_BU_ACCESS][1][0], $userroleid)->value($this->tables[self::TBL_ALL_BU_ACCESS][1][1]);
        $rolescope = DB::table($this->tables[self::TBL_ROLE_RESOURCE][0])->where($this->tables[self::TBL_ROLE_RESOURCE][1][0], $userroleid)->pluck($this->tables[self::TBL_ROLE_RESOURCE][1][1]);
        session([
            self::CURRENT_USER => $sessiondata[0]['displayname'][0],
            'sam' => $sessiondata[0][self::SAM_ACCOUNT_NAME][0],
            'email' => $sessiondata[0]['mail'][0],
            self::ROLE_SCOPE => $rolescope,
            self::FILE_MANAGER => $filemanager,
            self::SYSTEM_ADMIN => $systemadmin,
            'password' => $password,
            'allbuaccess' => $allbuaccess
        ]);
    }
    
    public function userSessionCheck() {
        if(!empty(session(self::CURRENT_USER))){
            return response()->json([ self::CURRENT_USER => session(self::CURRENT_USER), 'session' => [ self::CURRENT_USER => session(self::CURRENT_USER), self::ROLE_SCOPE => session(self::ROLE_SCOPE), self::SYSTEM_ADMIN => session(self::SYSTEM_ADMIN), self::FILE_MANAGER => session(self::FILE_MANAGER) ] ]);
        }else{
            return response()->json([ self::CURRENT_USER => false ]);
        }
    }
    
    public function userSessionDestroy() {
        session()->flush();
        if(empty(session()->all())){
            return response()->json([ 'signout' => true ]);
        }
    }
}
