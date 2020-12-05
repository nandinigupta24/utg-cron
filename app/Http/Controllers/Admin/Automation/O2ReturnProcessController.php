<?php

namespace App\Http\Controllers\Admin\Automation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Artisan;
use App\Model\UTGAPI\O2ReturnProcessFile;
use App\Model\UTGAPI\O2ReturnProcessData;

class O2ReturnProcessController extends Controller {

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
        
        $data = O2ReturnProcessFile::orderBy('id','desc')->paginate(10);
//        pr($data);
//        exit;
        return view('Automation.O2ReturnProcess.file',compact(['data']));
    }
    public function data() {
        
        $data = O2ReturnProcessData::orderBy('id','desc')->paginate(10);
//        pr($data);
//        exit;
        return view('Automation.O2ReturnProcess.data',compact(['data']));
    }

}
