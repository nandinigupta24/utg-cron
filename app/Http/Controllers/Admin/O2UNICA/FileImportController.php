<?php

namespace App\Http\Controllers\Admin\O2UNICA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Artisan;
use App\User;
use Hash;
use Auth;
use Mail;
use App\Model\UTGAPI\FileImportLog;

class FileImportController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {

        $this->middleware('auth');
    }

    public function manage(Request $request) {
        $total = FileImportLog::count();
        $data = FileImportLog::where('file_extension','<>','mfst')->paginate(10);
        
        return view('admin.O2UNICA.file_import.manage', compact(['data','total']));
    }

    public function queue_add(Request $request){
        $fileID = $request->fileID;
        $FileImportLog = FileImportLog::find($fileID);
        $FileImportLog->api_queue = 'added';
        if($FileImportLog->save()){
            return 'This file data has been successfully added in API Queue!!';
        }
    }
}
