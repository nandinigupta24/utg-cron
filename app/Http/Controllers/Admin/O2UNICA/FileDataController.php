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
use App\Model\UTGAPI\O2UNICA;
use App\Model\UTGAPI\FileImportLog;

class FileDataController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {

        $this->middleware('auth');
    }

    public function manage(Request $request) {
        $data = O2UNICA::paginate(10);

        return view('admin.O2UNICA.file_data.manage', compact(['data', 'total']));
    }

}
