<?php

namespace App\Http\Controllers\Admin\Automation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Artisan;
use App\Model\UTGAPI\O2FreeSimFileImport;
use App\Model\UTGAPI\O2FreeSimLoadedRecord;
use Carbon\Carbon;

class O2FreeSimController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {

        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        
        $data = O2FreeSimFileImport::orderBy('id','desc')->paginate(10);
       
        return view('Automation.O2FreeSim.file',compact(['data']));
    }
    public function data(Request $request) {
        $O2FreeSimLoadedRecord = O2FreeSimLoadedRecord::orderBy('id','desc');
        if(!empty($request->start)){
           $start = Carbon::parse($request->start)->startOfDay()->toDateTimestring();
           $O2FreeSimLoadedRecord->where('created_at','>=',$start);
        }
        if(!empty($request->end)){
           $end = Carbon::parse($request->end)->startOfDay()->toDateTimestring();
           $O2FreeSimLoadedRecord->where('created_at','<=',$end);
        }
        if(!empty($request->dupe_status)){
           $O2FreeSimLoadedRecord->where('dupes_status',$request->dupe_status);
        }
        
        $data = $O2FreeSimLoadedRecord->paginate(10);
      
        return view('Automation.O2FreeSim.data',compact(['data']));
    }

}
